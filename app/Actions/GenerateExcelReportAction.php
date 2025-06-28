<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GenerateExcelReportAction
{
    use AsAction;

    public function handle(array $groupedProducts, array $excludedProducts, array $filteredProductsData): array
    {
        $reportData = [];
        
        // Tạo file kết quả chính
        $this->generateMainResultFile($groupedProducts, $reportData);
        
        // Tạo file báo cáo chi tiết
        $this->generateDetailedReport($reportData, $excludedProducts);
        
        // Tạo file gửi kho Trung Quốc
        $this->generateWarehouseFile($reportData, $filteredProductsData);
        
        return $reportData;
    }

    private function generateMainResultFile(array $groupedProducts, array &$reportData): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(date('d_m_Y'));

        // Thiết lập header
        $this->setupMainFileHeaders($sheet);

        // Điền dữ liệu
        $row = 2;
        foreach ($groupedProducts as $baseSku => $productInfo) {
            if ($productInfo['total_need'] >= 6) {
                $this->fillMainFileRow($sheet, $row, $baseSku, $productInfo, $reportData);
                $row++;
            }
        }

        // Định dạng file
        $this->formatMainFile($sheet, $row - 1);

        // Lưu file
        $outputPath = public_path('uploads/nhap_hang_trung_quoc.xlsx');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputPath);
    }

    private function setupMainFileHeaders($sheet): void
    {
        $sheet->setCellValue('A1', 'Hình ảnh');
        $sheet->setCellValue('B1', 'Tên sản phẩm');
        for ($i = 0; $i <= 9; $i++) {
            $sheet->setCellValue(chr(67 + $i) . '1', 36 + $i);
        }
        $sheet->setCellValue('L1', 'Tổng');
        $sheet->setCellValue('M1', 'Giá');
        $sheet->setCellValue('N1', 'Thành tiền');
        $sheet->setCellValue('O1', 'SKU');
    }

    private function fillMainFileRow($sheet, int $row, string $baseSku, array $productInfo, array &$reportData): void
    {
        $imageLinks = $productInfo['images'] ?? [];
        $sheet->setCellValue('A' . $row, implode("\n", $imageLinks));
        $sheet->getRowDimension($row)->setRowHeight(-1);

        $productName = $productInfo['name'] ?? $baseSku;
        $sheet->setCellValue('B' . $row, $productName);

        $total = 0;
        for ($i = 0; $i <= 9; $i++) {
            $size = 36 + $i;
            $amount = $productInfo['sizes'][$size] ?? 0;
            $sheet->setCellValue(chr(67 + $i) . $row, $amount);
            $total += $amount;
        }

        $reportData[] = [
            'sku' => $baseSku,
            'name' => $productName,
            'images' => $imageLinks,
            'sizes' => $productInfo['sizes'],
            'total' => $total,
            'amount' => $productInfo['total_need'],
            'optimized' => ($productInfo['total_need'] >= 12 ? 'Đã tối ưu' : ($productInfo['optimization_note'] ?? 'Chưa tối ưu')),
            'optimization_note' => $productInfo['optimization_note'] ?? ($productInfo['total_need'] >= 12 ? 'Đã tối ưu' : 'Chưa tối ưu'),
            'original_sizes' => $productInfo['original_sizes'] ?? []
        ];

        $sheet->setCellValue('L' . $row, $total);
        $sheet->setCellValue('M' . $row, 10);
        $sheet->setCellValue('N' . $row, '=L' . $row . '*M' . $row);
        $sheet->setCellValue('O' . $row, $baseSku);
    }

    private function formatMainFile($sheet, int $lastRow): void
    {
        $sheet->getStyle('A1:O1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4A90E2']],
        ]);
        $sheet->getStyle('A1:O' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('L2:N' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
        
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setWrapText(true);
    }

    private function generateDetailedReport(array $reportData, array $excludedProducts): void
    {
        $reportSpreadsheet = new Spreadsheet();
        $reportSheet = $reportSpreadsheet->getActiveSheet();
        $reportSheet->setTitle('Báo cáo nhập hàng');

        // Thiết lập header báo cáo
        $this->setupDetailedReportHeaders($reportSheet);

        // Điền dữ liệu báo cáo
        $this->fillDetailedReportData($reportSheet, $reportData);

        // Tạo sheet Log
        $this->createLogSheet($reportSpreadsheet, $excludedProducts);

        // Format và lưu
        $this->formatDetailedReport($reportSheet);
        $reportWriter = IOFactory::createWriter($reportSpreadsheet, 'Xlsx');
        $reportWriter->setPreCalculateFormulas(false);
        $reportWriter->save(public_path('uploads/nhap_hang_trung_quoc.xlsx'));
    }

    private function setupDetailedReportHeaders($sheet): void
    {
        $sheet->setCellValue('A1', 'Hình ảnh URL');
        $sheet->setCellValue('B1', 'Hình ảnh');
        $sheet->setCellValue('C1', 'Tên sản phẩm');
        $sheet->setCellValue('D1', 'SKU');
        for ($i = 0; $i <= 9; $i++) {
            $sheet->setCellValue(chr(69 + $i) . '1', 'Size ' . (36 + $i));
        }
        $sheet->setCellValue('O1', 'Tổng');
        $sheet->setCellValue('P1', 'Ghi chú');
    }

    private function fillDetailedReportData($sheet, array $reportData): void
    {
        $reportRow = 2;
        foreach ($reportData as $data) {
            $imageUrl = isset($data['images'][0]) ? $data['images'][0] : '';
            $sheet->setCellValue('A' . $reportRow, $imageUrl);
            $sheet->setCellValue('B' . $reportRow, '=IMAGE(A' . $reportRow . ',2)');
            $sheet->setCellValue('C' . $reportRow, $data['name'] ?? $data['sku']);
            $sheet->setCellValue('D' . $reportRow, $data['sku']);

            $total = 0;
            $originalSizes = $data['original_sizes'] ?? [];
            for ($i = 0; $i <= 9; $i++) {
                $size = 36 + $i;
                $amount = $data['sizes'][$size] ?? 0;
                $sheet->setCellValue(chr(69 + $i) . $reportRow, $amount);
                $total += $amount;

                // Tô màu cho size gốc và size được thêm
                if (in_array($size, $originalSizes) && $amount > 0) {
                    $sheet->getStyle(chr(69 + $i) . $reportRow)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A9DFBF']]
                    ]);
                } else if ($amount > 0) {
                    $sheet->getStyle(chr(69 + $i) . $reportRow)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3CD']]
                    ]);
                }
            }
            $sheet->setCellValue('O' . $reportRow, $total);
            $sheet->setCellValue('P' . $reportRow, $data['optimization_note']);
            $reportRow++;
        }
    }

    private function createLogSheet($reportSpreadsheet, array $excludedProducts): void
    {
        $logSheet = $reportSpreadsheet->createSheet();
        $logSheet->setTitle('Log');
        
        // Setup headers
        $logSheet->setCellValue('A1', 'Hình ảnh URL');
        $logSheet->setCellValue('B1', 'Hình ảnh');
        $logSheet->setCellValue('C1', 'Tên sản phẩm');
        $logSheet->setCellValue('D1', 'SKU');
        for ($i = 0; $i <= 9; $i++) {
            $logSheet->setCellValue(chr(69 + $i) . '1', 'Size ' . (36 + $i));
        }
        $logSheet->setCellValue('O1', 'Tổng');
        $logSheet->setCellValue('P1', 'Cần');
        $logSheet->setCellValue('Q1', 'Đang về');
        $logSheet->setCellValue('R1', 'Lý do không nhập');

        // Fill data
        $logRow = 2;
        foreach ($excludedProducts as $baseSku => $data) {
            $imageUrl = isset($data['images'][0]) ? $data['images'][0] : '';
            $logSheet->setCellValue('A' . $logRow, $imageUrl);
            $logSheet->setCellValue('B' . $logRow, '=IMAGE(A' . $logRow . ',2)');
            $logSheet->setCellValue('C' . $logRow, $data['name'] ?? $baseSku);
            $logSheet->setCellValue('D' . $logRow, $baseSku);

            $total = 0;
            $allReasons = [];
            for ($i = 0; $i <= 9; $i++) {
                $size = 36 + $i;
                $amount = $data['sizes'][$size] ?? 0;
                $logSheet->setCellValue(chr(69 + $i) . $logRow, $amount);
                $total += $amount;
                if (isset($data['reasons'][$size]) && !empty($data['reasons'][$size])) {
                    $allReasons[$size] = $data['reasons'][$size];
                }
            }

            $logSheet->setCellValue('O' . $logRow, $total);
            
            $totalNeed = 0;
            $totalComing = 0;
            if (isset($data['original_data'])) {
                foreach ($data['original_data'] as $size => $info) {
                    $totalNeed += $info['need'] ?? 0;
                    $totalComing += $info['coming'] ?? 0;
                }
            }
            $logSheet->setCellValue('P' . $logRow, $totalNeed);
            $logSheet->setCellValue('Q' . $logRow, $totalComing);

            if (count($allReasons) > 1) {
                $formattedReasons = array_map(function ($size, $reason) {
                    return "Size $size: $reason";
                }, array_keys($allReasons), $allReasons);
                $logSheet->setCellValue('R' . $logRow, implode("\n", $formattedReasons));
            } else {
                $mainReason = reset($allReasons) ?: "Không xác định";
                $logSheet->setCellValue('R' . $logRow, $mainReason);
            }
            $logRow++;
        }

        // Format log sheet
        $this->formatLogSheet($logSheet, $logRow - 1);
    }

    private function formatDetailedReport($sheet): void
    {
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E9E9E9']],
        ]);
        $sheet->getStyle('A1:P' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setWrapText(true);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getStyle('C2:C' . $lastRow)->getAlignment()->setWrapText(true);
        $sheet->getColumnDimension('P')->setWidth(60);
        $sheet->getStyle('P2:P' . $lastRow)->getAlignment()->setWrapText(true);
        
        foreach (range('D', 'O') as $col) {
            $sheet->getStyle($col . '2:' . $col . $lastRow)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    private function formatLogSheet($sheet, int $lastRow): void
    {
        $sheet->getStyle('A1:R1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E9E9E9']],
        ]);
        $sheet->getStyle('A1:R' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setWrapText(true);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getStyle('C2:C' . $lastRow)->getAlignment()->setWrapText(true);
        $sheet->getColumnDimension('R')->setWidth(40);
        $sheet->getStyle('R2:R' . $lastRow)->getAlignment()->setWrapText(true);
        
        foreach (range('D', 'Q') as $col) {
            $sheet->getStyle($col . '2:' . $col . $lastRow)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    private function generateWarehouseFile(array $reportData, array $filteredProductsData): void
    {
        // Tạo sheet File gửi kho Trung Quốc trong file báo cáo chính
        $reportSpreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('uploads/nhap_hang_trung_quoc.xlsx'));
        $warehouseSheet = $reportSpreadsheet->createSheet();
        $warehouseSheet->setTitle('File gửi kho trung quốc');

        // Setup headers
        $warehouseSheet->setCellValue('A1', 'Hình ảnh');
        for ($i = 0; $i <= 9; $i++) {
            $warehouseSheet->setCellValue(chr(66 + $i) . '1', 'Size ' . (36 + $i));
        }
        $warehouseSheet->setCellValue('L1', 'Tổng');
        $warehouseSheet->setCellValue('M1', 'SKU');
        $warehouseSheet->setCellValue('N1', 'Giá nhập');
        $warehouseSheet->setCellValue('O1', 'Thành tiền');
        $warehouseSheet->setCellValue('P1', 'Tỷ giá');
        $warehouseSheet->setCellValue('Q1', 'Tổng tiền VND');

        // Lấy giá nhập từ dữ liệu sản phẩm
        $priceData = $this->getImportPrices($filteredProductsData);
        $exchangeRate = 3500; // Mặc định

        // Điền dữ liệu
        $warehouseRow = 2;
        foreach ($reportData as $data) {
            $baseSku = $data['sku'];
            $imageUrl = isset($data['images'][0]) ? $data['images'][0] : '';
            $importPrice = $priceData[$baseSku] ?? 0;

            $warehouseSheet->setCellValue('A' . $warehouseRow, $imageUrl);
            $total = 0;
            for ($i = 0; $i <= 9; $i++) {
                $size = 36 + $i;
                $amount = $data['sizes'][$size] ?? 0;
                if ($amount > 0) {
                    $warehouseSheet->setCellValue(chr(66 + $i) . $warehouseRow, $amount);
                }
                $total += $amount;
            }

            $warehouseSheet->setCellValue('L' . $warehouseRow, $total);
            $warehouseSheet->setCellValue('M' . $warehouseRow, $baseSku);
            $warehouseSheet->setCellValue('N' . $warehouseRow, $importPrice);
            $warehouseSheet->setCellValue('O' . $warehouseRow, '=L' . $warehouseRow . '*N' . $warehouseRow);
            $warehouseSheet->setCellValue('P' . $warehouseRow, $exchangeRate);
            $warehouseSheet->setCellValue('Q' . $warehouseRow, '=O' . $warehouseRow . '*P' . $warehouseRow);

            $warehouseRow += 2;
        }

        // Format warehouse sheet
        $this->formatWarehouseSheet($warehouseSheet, $warehouseRow - 1);

        // Lưu file
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($reportSpreadsheet, 'Xlsx');
        $writer->setPreCalculateFormulas(false);
        $writer->save(public_path('uploads/nhap_hang_trung_quoc.xlsx'));
    }

    private function getImportPrices(array $filteredProductsData): array
    {
        $priceData = [];
        foreach ($filteredProductsData as $row) {
            if (isset($row['N']) && !empty($row['N'])) {
                $skuParts = explode('-', $row['N']);
                $baseSku = $skuParts[0];
                if (isset($row['AG']) && is_numeric(str_replace([',', '.'], '', $row['AG']))) {
                    $importPrice = str_replace([',', '.'], '', $row['AG']);
                    $priceData[$baseSku] = (float)$importPrice;
                }
            }
        }
        return $priceData;
    }

    private function formatWarehouseSheet($sheet, int $lastRow): void
    {
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E9E9E9']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
        $sheet->getStyle('A1:Q' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('N2:Q' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');

        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('A')->setWidth(20);

        foreach (range('B', 'L') as $col) {
            $sheet->getStyle($col . '2:' . $col . $lastRow)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }
}

