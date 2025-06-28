<?php

namespace App\Http\Controllers;

use App\Actions\ImportExcelAction;
use App\Actions\ProcessChinaImportAction;
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
}