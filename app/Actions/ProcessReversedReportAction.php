<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Log;

class ProcessReversedReportAction
{
    use AsAction;

    public function handle($sapoFile, $reportFile): string
    {
        try {
            // Lưu 2 file upload
            $sapoPath = $this->saveUploadedFile($sapoFile, 'sapo');
            $reportPath = $this->saveUploadedFile($reportFile, 'report');

            // Đọc dữ liệu từ file Sapo gốc để lấy giá
            $sapoData = $this->readSapoData($sapoPath);

            // Đọc dữ liệu từ file báo cáo Trung Quốc đã chỉnh sửa
            $reportData = $this->getReportData($reportPath);

            // Debug: Log danh sách SKU được đọc từ file báo cáo
            $debugSkus = [];
            foreach ($reportData as $data) {
                $debugSkus[] = $data['sku'] . ' (sizes: ' . implode(',', array_keys($data['sizes'])) . ')';
            }
            Log::info('SKUs đọc từ file báo cáo Trung Quốc:', $debugSkus);

            // Lọc file Sapo dựa trên danh sách SKU từ báo cáo Trung Quốc
            $this->filterSapoBasedOnReport($reportData, $sapoData);

            // Xóa file tạm
            foreach ([$sapoPath, $reportPath] as $filePath) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $totalItems = 0;
            foreach ($reportData as $data) {
                $totalItems += count($data['sizes']);
            }

            return "Lọc file Sapo thành công!\n" .
                   "- Đã đọc " . count($reportData) . " sản phẩm từ file báo cáo Trung Quốc\n" .
                   "- Tổng " . $totalItems . " dòng sản phẩm (SKU-Size)\n" .
                   "- File Sapo đã được lọc: nhap_hang_sapo.xlsx\n" .
                   "- Debug: Kiểm tra log để xem danh sách SKU được đọc";

        } catch (\Exception $e) {
            return "Lỗi khi xử lý file: " . $e->getMessage() . " tại dòng " . $e->getLine();
        }
    }

    private function saveUploadedFile($uploadedFile, $type): string
    {
        $fileName = $type . '_' . time() . '.' . $uploadedFile->getClientOriginalExtension();
        $filePath = public_path('uploads/' . $fileName);
        $uploadedFile->move(public_path('uploads'), $fileName);

        return $filePath;
    }

    private function readSapoData(string $filePath): array
    {
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $sapoData = [];

            $row = 8; // File Sapo bắt đầu từ dòng 8
            $maxRows = $sheet->getHighestRow();

            while ($row <= $maxRows) {
                $sku = $sheet->getCell('A' . $row)->getValue();

                // Dừng nếu không có SKU
                if ($sku === null || $sku === '' || trim($sku) === '') {
                    break;
                }

                $barcode = $sheet->getCell('B' . $row)->getValue();
                $name = $sheet->getCell('C' . $row)->getValue();
                $quantity = $sheet->getCell('D' . $row)->getValue();
                $unit = $sheet->getCell('E' . $row)->getValue();
                $category = $sheet->getCell('F' . $row)->getValue();
                $brand = $sheet->getCell('G' . $row)->getValue();
                $supplier = $sheet->getCell('H' . $row)->getValue();
                $price = $sheet->getCell('I' . $row)->getValue();
                $retailPrice = $sheet->getCell('J' . $row)->getValue();

                $sapoData[] = [
                    'sku' => trim($sku),
                    'barcode' => $barcode,
                    'name' => $name,
                    'quantity' => $quantity,
                    'unit' => $unit,
                    'category' => $category,
                    'brand' => $brand,
                    'supplier' => $supplier,
                    'price' => $price,
                    'retail_price' => $retailPrice
                ];

                $row++;
            }

            Log::info('Đọc được ' . count($sapoData) . ' dòng từ file Sapo');
            return $sapoData;
        } catch (\Exception $e) {
            Log::error('Lỗi đọc file Sapo: ' . $e->getMessage());
            return [];
        }
    }

    private function getReportData(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);

        // Ưu tiên đọc từ sheet "File gửi kho trung quốc" trước
        $sheetNames = $spreadsheet->getSheetNames();
        $targetSheet = null;

        // Tìm sheet "File gửi kho trung quốc" trước
        foreach ($sheetNames as $sheetName) {
            if (strpos($sheetName, 'File gửi kho') !== false || strpos($sheetName, 'kho trung quốc') !== false) {
                $targetSheet = $spreadsheet->getSheetByName($sheetName);
                break;
            }
        }

        // Nếu không có thì tìm sheet "Báo cáo nhập hàng"
        if (!$targetSheet) {
            foreach ($sheetNames as $sheetName) {
                if (strpos($sheetName, 'Báo cáo') !== false || strpos($sheetName, 'báo cáo') !== false) {
                    $targetSheet = $spreadsheet->getSheetByName($sheetName);
                    break;
                }
            }
        }

        if (!$targetSheet) {
            $targetSheet = $spreadsheet->getActiveSheet();
        }

        $reportData = [];
        $row = 2;
        $maxRows = $targetSheet->getHighestRow();

        // Kiểm tra format của sheet để xử lý đúng cách
        $isWarehouseFormat = $this->isWarehouseFormat($targetSheet);

        if ($isWarehouseFormat) {
            // Format sheet "File gửi kho trung quốc": mỗi sản phẩm có 2 dòng (tiêu đề + dữ liệu)
            $row = 1;
            while ($row <= $maxRows) {
                // Tìm dòng có SKU (dòng dữ liệu)
                $skuValue = $targetSheet->getCell('O' . $row)->getValue();

                if ($skuValue !== null && $skuValue !== '' && trim($skuValue) !== '') {
                    $sku = trim($skuValue);
                    $name = $sku; // Warehouse format không có tên riêng

                    // Đọc số lượng theo size từ cột B đến K (Size 36-45)
                    $sizes = [];
                    $hasValidQuantity = false;

                    for ($i = 0; $i <= 9; $i++) {
                        $size = 36 + $i;
                        $colLetter = chr(66 + $i); // B, C, D, E, F, G, H, I, J, K
                        $cellValue = $targetSheet->getCell($colLetter . $row)->getValue();

                        if ($cellValue !== null && $cellValue !== '' && is_numeric($cellValue)) {
                            $quantity = (int)$cellValue;
                            if ($quantity > 0) {
                                $sizes[$size] = $quantity;
                                $hasValidQuantity = true;
                            }
                        }
                    }

                    if (!empty($sku) && $hasValidQuantity && !empty($sizes)) {
                        $reportData[] = [
                            'sku' => $sku,
                            'name' => $name,
                            'sizes' => $sizes
                        ];
                    }
                }
                $row++;
            }
        } else {
            // Format sheet "Báo cáo nhập hàng": format bảng thông thường
            while ($row <= $maxRows) {
                $skuValue = $targetSheet->getCell('D' . $row)->getValue();

                if ($skuValue === null || $skuValue === '' || trim($skuValue) === '') {
                    $row++;
                    continue;
                }

                $sku = trim($skuValue);
                $name = trim($targetSheet->getCell('C' . $row)->getValue() ?? '');

                // Đọc số lượng theo size từ cột E đến N (Size 36-45)
                $sizes = [];
                $hasValidQuantity = false;

                for ($i = 0; $i <= 9; $i++) {
                    $size = 36 + $i;
                    $colLetter = chr(69 + $i); // E, F, G, H, I, J, K, L, M, N
                    $cellValue = $targetSheet->getCell($colLetter . $row)->getValue();

                    if ($cellValue !== null && $cellValue !== '' && is_numeric($cellValue)) {
                        $quantity = (int)$cellValue;
                        if ($quantity > 0) {
                            $sizes[$size] = $quantity;
                            $hasValidQuantity = true;
                        }
                    }
                }

                if (!empty($sku) && $hasValidQuantity && !empty($sizes)) {
                    $reportData[] = [
                        'sku' => $sku,
                        'name' => $name ?: $sku,
                        'sizes' => $sizes
                    ];
                }

                $row++;
            }
        }

        return $reportData;
    }

    private function filterSapoBasedOnReport(array $reportData, array $sapoData): void
    {
        $sapoFilePath = public_path('uploads/nhap_hang_sapo.xlsx');

        // Tạo danh sách SKU từ báo cáo Trung Quốc
        $allowedSKUs = [];
        foreach ($reportData as $data) {
            foreach ($data['sizes'] as $size => $quantity) {
                if ($quantity > 0) {
                    $fullSku = $data['sku'] . '-' . $size;
                    $allowedSKUs[] = $fullSku;
                }
            }
        }

        Log::info('Danh sách SKU được phép từ báo cáo TQ:', $allowedSKUs);

        // Lọc dữ liệu Sapo chỉ giữ lại những SKU có trong báo cáo Trung Quốc
        $filteredSapoData = [];
        foreach ($sapoData as $item) {
            if (in_array($item['sku'], $allowedSKUs)) {
                $filteredSapoData[] = $item;
            }
        }

        Log::info('Số dòng Sapo sau khi lọc:', ['count' => count($filteredSapoData)]);

        // Sử dụng template Sapo gốc để giữ nguyên format
        $templateFilePath = public_path('uploads/nhap_hang_sapo_template.xlsx');

        // Tạo template nếu chưa có
        if (!file_exists($templateFilePath)) {
            $this->createSapoTemplate($templateFilePath);
        }

        // Copy template để giữ nguyên format
        copy($templateFilePath, $sapoFilePath);

        // Đọc và cập nhật file Sapo với dữ liệu đã lọc
        $spreadsheet = IOFactory::load($sapoFilePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Xóa dữ liệu cũ từ dòng 8 trở đi
        $maxRows = $sheet->getHighestRow();
        if ($maxRows >= 8) {
            $sheet->removeRow(8, $maxRows - 7);
        }

        // Điền dữ liệu đã lọc từ dòng 8
        $row = 8;
        foreach ($filteredSapoData as $item) {
            $sheet->setCellValue('A' . $row, $item['sku']);
            $sheet->setCellValue('B' . $row, $item['barcode']);
            $sheet->setCellValue('C' . $row, $item['name']);
            $sheet->setCellValue('D' . $row, $item['quantity']);
            $sheet->setCellValue('E' . $row, $item['unit'] ?? 'Cái');
            $sheet->setCellValue('F' . $row, $item['category'] ?? '');
            $sheet->setCellValue('G' . $row, $item['brand'] ?? '');
            $sheet->setCellValue('H' . $row, $item['supplier'] ?? '');
            $sheet->setCellValue('I' . $row, $item['price']);
            $sheet->setCellValue('J' . $row, $item['retail_price'] ?? '');
            $row++;
        }

        // Lưu file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($sapoFilePath);
    }

    private function createSapoTemplate(string $templateFilePath): void
    {
        $emptySpreadsheet = new Spreadsheet();
        $sheet = $emptySpreadsheet->getActiveSheet();

        // Tạo header giống như file Sapo gốc
        $sheet->setCellValue('A7', 'Mã SKU');
        $sheet->setCellValue('B7', 'Mã Barcode');
        $sheet->setCellValue('C7', 'Tên sản phẩm');
        $sheet->setCellValue('D7', 'Số lượng');
        $sheet->setCellValue('E7', 'Đơn vị tính');
        $sheet->setCellValue('F7', 'Danh mục');
        $sheet->setCellValue('G7', 'Thương hiệu');
        $sheet->setCellValue('H7', 'Nhà cung cấp');
        $sheet->setCellValue('I7', 'Đơn giá');
        $sheet->setCellValue('J7', 'Giá bán lẻ');

        $writer = IOFactory::createWriter($emptySpreadsheet, 'Xlsx');
        $writer->save($templateFilePath);
    }

    private function isWarehouseFormat($sheet): bool
    {
        // Kiểm tra xem có phải format warehouse không bằng cách:
        // 1. Kiểm tra cột O có chứa "SKU" hoặc có dữ liệu SKU
        // 2. Kiểm tra cột L có chứa "Pairs"

        $maxRows = min(10, $sheet->getHighestRow()); // Chỉ kiểm tra 10 dòng đầu

        for ($row = 1; $row <= $maxRows; $row++) {
            $colL = $sheet->getCell('L' . $row)->getValue();
            $colO = $sheet->getCell('O' . $row)->getValue();

            // Nếu tìm thấy "Pairs" và "SKU" thì đây là warehouse format
            if ((strpos($colL, 'Pairs') !== false || strpos($colL, 'pairs') !== false) &&
                (strpos($colO, 'SKU') !== false || strpos($colO, 'sku') !== false)) {
                return true;
            }
        }

        return false;
    }

}
