<?php

namespace App\Http\Controllers;

use App\Actions\ImportExcelAction;
use App\Actions\ProcessChinaImportAction;
use App\Actions\ProcessReversedReportAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Hiển thị form import Excel
     */
    public function form_import_excel()
    {
        return view('shop.form_import_excel');
    }

    /**
     * Xử lý import Excel sản phẩm
     */
    public function import_excel(Request $request)
    {
        return ImportExcelAction::run($request);
    }

    /**
     * Hiển thị form nhập hàng Trung Quốc
     */
    public function form_nhap_hang()
    {
        return view('shop.form_nhap_hang')->with([
            'title' => 'Nhập hàng trung quốc',
            'header' => 'Nhập hàng trung quốc'
        ]);
    }

    /**
     * Xử lý nhập hàng Trung Quốc
     */
    public function nhap_hang(Request $request)
    {
        $result = ProcessChinaImportAction::run($request);

        // Kiểm tra nếu kết quả bắt đầu bằng "Lỗi" thì hiển thị lỗi
        if (strpos($result, 'Lỗi') === 0) {
            return back()->with('error', $result);
        }

        // Nếu thành công, hiển thị thông báo thành công và thêm flag để hiển thị nút download
        return back()->with([
            'success' => $result,
            'report_filename' => 'nhap_hang_trung_quoc.xlsx'
        ]);
    }

    /**
     * Tải xuống file báo cáo nhập hàng
     */
    public function download_nhap_hang_report()
    {
        $reportPath = public_path('uploads/nhap_hang_trung_quoc.xlsx');

        if (!file_exists($reportPath)) {
            return back()->with('error', 'File báo cáo không tồn tại.');
        }

        return response()->download($reportPath, 'nhap_hang_trung_quoc_' . date('Y_m_d_H_i_s') . '.xlsx');
    }

    /**
     * Tải xuống file Sapo
     */
    public function download_nhap_hang_sapo()
    {
        $reportPath = public_path('uploads/nhap_hang_sapo.xlsx');

        if (!file_exists($reportPath)) {
            return back()->with('error', 'File Sapo không tồn tại.');
        }

        return response()->download($reportPath, 'nhap_hang_sapo_' . date('Y_m_d_H_i_s') . '.xlsx');
    }

    /**
     * Hiển thị flowchart quy trình nhập hàng
     */
    public function nhap_hang_flowchart()
    {
        return view('admin.nhap_hang_flowchart');
    }

    /**
     * Hiển thị form cập nhật file báo cáo
     */
    public function form_update_report()
    {
        return view('shop.form_update_report')->with([
            'title' => 'Cập nhật file báo cáo',
            'header' => 'Cập nhật file báo cáo'
        ]);
    }

    /**
     * Lọc file Sapo dựa trên 2 file đã được tạo từ /tq
     */
    public function process_reversed_report(Request $request)
    {
        $request->validate([
            'sapo_file' => 'required|file|mimes:xlsx,xls',
            'report_file' => 'required|file|mimes:xlsx,xls'
        ]);

        $result = ProcessReversedReportAction::run(
            $request->file('sapo_file'),
            $request->file('report_file')
        );

        // Kiểm tra nếu kết quả bắt đầu bằng "Lỗi" thì hiển thị lỗi
        if (strpos($result, 'Lỗi') === 0) {
            return back()->with('error', $result);
        }

        // Nếu thành công, hiển thị thông báo thành công
        return back()->with([
            'success' => $result,
            'sapo_regenerated' => true
        ]);
    }

    /**
     * Debug file báo cáo để xem danh sách SKU
     */
    public function debug_report_file(Request $request)
    {
        $request->validate([
            'report_file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            // Lưu file tạm
            $reportFile = $request->file('report_file');
            $fileName = 'debug_' . time() . '.' . $reportFile->getClientOriginalExtension();
            $filePath = public_path('uploads/' . $fileName);
            $reportFile->move(public_path('uploads'), $fileName);

            // Đọc dữ liệu từ file báo cáo
            $debugResult = $this->debugReadReportData($filePath);
            $reportData = $debugResult['data'];
            $sheetName = $debugResult['sheet_name'];

            // Xóa file tạm
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Tạo output debug
            $output = "=== DEBUG: DANH SÁCH SKU TỪ FILE BÁO CÁO TRUNG QUỐC ===\n\n";
            $output .= "📋 Sheet được đọc: " . $sheetName . "\n";
            $output .= "📊 Tổng số sản phẩm đọc được: " . count($reportData) . "\n\n";

            if (empty($reportData)) {
                $output .= "❌ KHÔNG ĐỌC ĐƯỢC DỮ LIỆU NÀO!\n";
                $output .= "Kiểm tra lại:\n";
                $output .= "- File có sheet 'Báo cáo nhập hàng' không?\n";
                $output .= "- Dữ liệu bắt đầu từ dòng 2?\n";
                $output .= "- Cột D có chứa SKU không?\n";
                $output .= "- Các cột E-N có chứa số lượng không?\n";
            } else {
                foreach ($reportData as $index => $data) {
                    $output .= ($index + 1) . ". SKU: " . $data['sku'] . "\n";
                    $output .= "   Tên: " . $data['name'] . "\n";
                    $output .= "   Sizes có số lượng: ";

                    $sizeList = [];
                    foreach ($data['sizes'] as $size => $quantity) {
                        $sizeList[] = "Size $size ($quantity đôi)";
                    }
                    $output .= implode(', ', $sizeList) . "\n";

                    $output .= "   → Tạo SKU-Size: ";
                    $skuSizeList = [];
                    foreach ($data['sizes'] as $size => $quantity) {
                        $skuSizeList[] = $data['sku'] . '-' . $size;
                    }
                    $output .= implode(', ', $skuSizeList) . "\n\n";
                }
            }

            return response($output, 200, ['Content-Type' => 'text/plain; charset=utf-8']);

        } catch (\Exception $e) {
            return response("Lỗi khi debug file: " . $e->getMessage() . " tại dòng " . $e->getLine(), 500, ['Content-Type' => 'text/plain; charset=utf-8']);
        }
    }

    private function debugReadReportData(string $filePath): array
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);

        // Ưu tiên đọc từ sheet "File gửi kho trung quốc" trước
        $sheetNames = $spreadsheet->getSheetNames();
        $targetSheet = null;
        $selectedSheetName = '';

        // Tìm sheet "File gửi kho trung quốc" trước
        foreach ($sheetNames as $sheetName) {
            if (strpos($sheetName, 'File gửi kho') !== false || strpos($sheetName, 'kho trung quốc') !== false) {
                $targetSheet = $spreadsheet->getSheetByName($sheetName);
                $selectedSheetName = $sheetName;
                break;
            }
        }

        // Nếu không có thì tìm sheet "Báo cáo nhập hàng"
        if (!$targetSheet) {
            foreach ($sheetNames as $sheetName) {
                if (strpos($sheetName, 'Báo cáo') !== false || strpos($sheetName, 'báo cáo') !== false) {
                    $targetSheet = $spreadsheet->getSheetByName($sheetName);
                    $selectedSheetName = $sheetName;
                    break;
                }
            }
        }

        if (!$targetSheet) {
            $targetSheet = $spreadsheet->getActiveSheet();
            $selectedSheetName = $targetSheet->getTitle();
        }

        $reportData = [];
        $row = 2;
        $maxRows = $targetSheet->getHighestRow();

        // Kiểm tra format của sheet
        $isWarehouseFormat = $this->isWarehouseFormat($targetSheet);

        if ($isWarehouseFormat) {
            // Format sheet "File gửi kho trung quốc"
            $row = 1;
            while ($row <= $maxRows) {
                $skuValue = $targetSheet->getCell('O' . $row)->getValue();

                if ($skuValue !== null && $skuValue !== '' && trim($skuValue) !== '') {
                    $sku = trim($skuValue);
                    $name = $sku;

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
            // Format sheet "Báo cáo nhập hàng"
            while ($row <= $maxRows) {
                $skuValue = $targetSheet->getCell('D' . $row)->getValue();

                if ($skuValue === null || $skuValue === '' || trim($skuValue) === '') {
                    $row++;
                    continue;
                }

                $sku = trim($skuValue);
                $name = trim($targetSheet->getCell('C' . $row)->getValue() ?? '');

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

        return [
            'data' => $reportData,
            'sheet_name' => $selectedSheetName
        ];
    }

    private function isWarehouseFormat($sheet): bool
    {
        $maxRows = min(10, $sheet->getHighestRow());

        for ($row = 1; $row <= $maxRows; $row++) {
            $colL = $sheet->getCell('L' . $row)->getValue();
            $colO = $sheet->getCell('O' . $row)->getValue();

            if ((strpos($colL, 'Pairs') !== false || strpos($colL, 'pairs') !== false) &&
                (strpos($colO, 'SKU') !== false || strpos($colO, 'sku') !== false)) {
                return true;
            }
        }

        return false;
    }
}