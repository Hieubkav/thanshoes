<?php

namespace App\Actions;

use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProcessChinaImportAction
{
    use AsAction;

    public function handle(Request $request): string
    {
        // Tăng thời gian thực thi
        set_time_limit(300);

        // Validate đầu vào
        $request->validate([
            'excel_products' => 'required|file',
            'excel_low_stock' => 'required|file',
            'exchange_rate' => 'required|numeric',
        ]);

        try {
            // Kiểm tra phần mở rộng file
            $this->validateFileExtensions($request);

            // Lưu file
            $this->saveUploadedFiles($request);

            // Đọc dữ liệu từ file Excel
            $productsData = $this->readExcelFile(public_path('uploads/data_shoes.xlsx'));
            $lowStockData = $this->readExcelFile(public_path('uploads/cannhap.xls'));

            // Xử lý dữ liệu từ file báo cáo sản phẩm sắp hết
            $processedData = ProcessLowStockDataAction::run($lowStockData);
            $groupedProducts = $processedData['valid'];
            $excludedProducts = $processedData['excluded'];

            // Lọc dữ liệu sản phẩm dựa trên SKU hợp lệ
            $validBaseSkus = array_keys($groupedProducts);
            $filteredProductsData = $this->filterProductsData($productsData, $validBaseSkus);

            // Thu thập thông tin và tối ưu số lượng
            $groupedProducts = OptimizeProductQuantityAction::run($groupedProducts, $filteredProductsData, $lowStockData);
            $excludedProducts = $this->collectProductDetailsForExcluded($excludedProducts, $filteredProductsData);

            // Tạo các file Excel báo cáo
            $reportData = GenerateExcelReportAction::run($groupedProducts, $excludedProducts, $filteredProductsData);

            // Tạo file nhập hàng cho Sapo
            $this->generateSapoFile($reportData, $filteredProductsData, $request->input('exchange_rate', 3500));

            // Cập nhật file cannhap.xls
            $this->updateLowStockFile($lowStockData, $groupedProducts, $excludedProducts);

            // Trả về kết quả
            $reportFilename = 'nhap_hang_trung_quoc.xlsx';
            $totalProducts = count($groupedProducts);
            $totalExcluded = count($excludedProducts);
            $totalQuantity = array_sum(array_column($reportData, 'total'));

            return "Xử lý thành công!\n" .
                "- Sản phẩm hợp lệ: $totalProducts\n" .
                "- Sản phẩm bị loại: $totalExcluded\n" .
                "- Tổng số lượng nhập: $totalQuantity đôi\n" .
                "- File báo cáo: $reportFilename\n" .
                "- File Sapo: nhap_hang_sapo.xlsx";

        } catch (\Exception $e) {
            return "Lỗi khi xử lý: " . $e->getMessage() . " tại dòng " . $e->getLine();
        }
    }

    private function validateFileExtensions(Request $request): void
    {
        $allowedExtensions = ['xlsx', 'xls'];
        $productsExtension = strtolower($request->file('excel_products')->getClientOriginalExtension());
        $lowStockExtension = strtolower($request->file('excel_low_stock')->getClientOriginalExtension());

        if (!in_array($productsExtension, $allowedExtensions)) {
            throw new \Exception('File danh sách sản phẩm phải có định dạng .xlsx hoặc .xls');
        }
        if (!in_array($lowStockExtension, $allowedExtensions)) {
            throw new \Exception('File báo cáo sản phẩm sắp hết phải có định dạng .xlsx hoặc .xls');
        }
    }

    private function saveUploadedFiles(Request $request): void
    {
        // Đảm bảo thư mục uploads tồn tại
        $directory = public_path('uploads');
        if (!file_exists($directory)) {
            if (!mkdir($directory, 0777, true)) {
                throw new \Exception("Không thể tạo thư mục: " . $directory);
            }
        }

        // Lưu file với tên cố định
        $request->file('excel_products')->move($directory, 'data_shoes.xlsx');
        $request->file('excel_low_stock')->move($directory, 'cannhap.xls');
    }

    private function readExcelFile(string $filePath): array
    {
        return IOFactory::load($filePath)
            ->getActiveSheet()
            ->toArray(null, true, true, true);
    }

    private function filterProductsData(array $productsData, array $validBaseSkus): array
    {
        return array_filter($productsData, function ($row) use ($validBaseSkus) {
            if (!isset($row['N'])) return false;
            $skuParts = explode('-', $row['N']);
            $baseSku = $skuParts[0];
            return in_array($baseSku, $validBaseSkus);
        });
    }

    private function collectProductDetailsForExcluded(array $excludedProducts, array $filteredProductsData): array
    {
        foreach ($excludedProducts as $baseSku => &$productInfo) {
            $productInfo['images'] = [];
            $productInfo['name'] = $baseSku;

            foreach ($filteredProductsData as $row) {
                if (isset($row['N']) && strpos($row['N'], $baseSku) === 0) {
                    if (!in_array($row['R'], $productInfo['images']) && !empty($row['R'])) {
                        $productInfo['images'][] = $row['R'];
                    }
                    foreach (['P', 'Q'] as $col) {
                        if (!in_array($row[$col], $productInfo['images']) && !empty($row[$col])) {
                            $productInfo['images'][] = $row[$col];
                        }
                    }
                    if (!empty($row['A'])) {
                        $productInfo['name'] = $row['A'];
                    }
                }
            }
        }

        return $excludedProducts;
    }

    private function generateSapoFile(array $reportData, array $filteredProductsData, float $exchangeRate): void
    {
        $templateFilePath = public_path('uploads/nhap_hang_sapo_template.xlsx');
        $outputFilePath = public_path('uploads/nhap_hang_sapo.xlsx');

        // Tạo template nếu chưa có
        if (!file_exists($templateFilePath)) {
            $this->createSapoTemplate($templateFilePath);
        }

        // Copy template
        copy($templateFilePath, $outputFilePath);

        // Đọc và cập nhật file Sapo
        $spreadsheet = IOFactory::load($outputFilePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Lấy giá bán buôn từ data_shoes.xlsx
        $wholesalePrices = $this->getWholesalePrices($filteredProductsData);

        // Điền dữ liệu vào file Sapo
        $row = 8; // Bắt đầu từ dòng 8
        foreach ($reportData as $data) {
            $baseSku = $data['sku'];
            $wholesalePrice = $wholesalePrices[$baseSku] ?? 0;
            $totalQuantity = $data['total'];

            $sheet->setCellValue('A' . $row, $baseSku);
            $sheet->setCellValue('B' . $row, $baseSku);
            $sheet->setCellValue('C' . $row, $data['name']);
            $sheet->setCellValue('D' . $row, $totalQuantity);
            $sheet->setCellValue('I' . $row, $wholesalePrice);
            $row++;
        }

        // Lưu file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputFilePath);
    }

    private function createSapoTemplate(string $templateFilePath): void
    {
        $emptySpreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $emptySpreadsheet->getActiveSheet();
        $sheet->setCellValue('A7', 'Mã SKU');
        $sheet->setCellValue('B7', 'Mã Barcode');
        $sheet->setCellValue('C7', 'Tên sản phẩm');
        $sheet->setCellValue('D7', 'Số lượng');
        $sheet->setCellValue('I7', 'Đơn giá');

        $writer = IOFactory::createWriter($emptySpreadsheet, 'Xlsx');
        $writer->save($templateFilePath);
    }

    private function getWholesalePrices(array $filteredProductsData): array
    {
        $wholesalePrices = [];
        foreach ($filteredProductsData as $row) {
            if (isset($row['N']) && !empty($row['N'])) {
                $skuParts = explode('-', $row['N']);
                $baseSku = $skuParts[0];
                if (isset($row['AB']) && is_numeric(str_replace([',', '.'], '', $row['AB']))) {
                    $wholesalePrice = str_replace([',', '.'], '', $row['AB']);
                    $wholesalePrices[$baseSku] = (float)$wholesalePrice;
                }
            }
        }
        return $wholesalePrices;
    }

    private function updateLowStockFile(array $lowStockData, array $groupedProducts, array $excludedProducts): void
    {
        $filePath = public_path('uploads/cannhap.xls');
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Cập nhật dữ liệu cho các sản phẩm hợp lệ
        foreach ($groupedProducts as $baseSku => $productInfo) {
            if (isset($productInfo['original_data'])) {
                foreach ($productInfo['original_data'] as $size => $info) {
                    $row = $info['row'];
                    $newQuantity = $productInfo['sizes'][$size] ?? 0;
                    $sheet->setCellValue('I' . $row, $newQuantity);
                }
            }
        }

        // Cập nhật dữ liệu cho các sản phẩm bị loại
        foreach ($excludedProducts as $baseSku => $productInfo) {
            if (isset($productInfo['original_data'])) {
                foreach ($productInfo['original_data'] as $size => $info) {
                    $row = $info['row'];
                    $sheet->setCellValue('I' . $row, 0);
                }
            }
        }

        // Lưu file
        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save($filePath);
    }
}
