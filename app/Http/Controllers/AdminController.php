<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Variant;
use App\Models\VariantImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminController extends Controller
{
    public function excel()
    {
        // Đọc file excel
        $filePath = public_path('uploads/data_shoes.xlsx');

        // Load file excel
        $spreadsheet = IOFactory::load($filePath);

        // Lấy ra dữ liệu của sheet đầu tiên
        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Mảng lưu trữ SKU hiện tại để kiểm tra sau
        $current_skus = [];

        // Mảng lưu kết quả để trả về
        $object_data = [];

        foreach ($data as $key => $value) {
            if ($value['A'] != '' && $key != 1) {
                $sku = $value['N'];
                // Thêm SKU vào mảng kiểm tra
                $current_skus[] = $sku;

                // Tách SKU để lấy mã sản phẩm (phần trước dấu -)
                $sku_parts = explode('-', $sku);
                $product_code = $sku_parts[0];

                // Tìm sản phẩm dựa vào pattern của SKU
                $existing_variant = Variant::where('sku', 'LIKE', $product_code.'-%')->first();
                
                if ($existing_variant) {
                    $product = $existing_variant->product;
                } else {
                    // Tạo sản phẩm mới nếu không tìm thấy SKU pattern
                    $product = new Product();
                }

                // Cập nhật thông tin sản phẩm
                $product->name = $value['A'];
                $product->description = $value['D'];
                $product->brand = $value['E'];
                $product->type = $value['C'];
                $product->save();

                // Xử lý biến thể
                $variant = Variant::where('sku', $sku)->first();
                if (!$variant) {
                    $variant = new Variant();
                    $variant->product_id = $product->id;
                    $variant->sku = $sku;
                }

                // Cập nhật thông tin biến thể
                $variant->color = $value['J'];
                $variant->size = $value['H'];
                $variant->price = str_replace(',', '', $value['AF']);
                $variant->stock = str_replace(',', '', $value['AA']);
                $variant->save();

                // Thêm ảnh mới cho variant nếu có
                if (!empty($value['R'])) {
                    $variant_image = new VariantImage();
                    $variant_image->variant_id = $variant->id;
                    $variant_image->image = $value['R'];
                    $variant_image->save();
                }

                // Thêm vào mảng kết quả
                $object_data[] = [
                    'name' => $value['A'],
                    'type' => $value['C'],
                    'description' => $value['D'],
                    'brand' => $value['E'],
                    'tags' => $value['F'],
                    'variants' => [
                        [
                            'size' => $value['H'],
                            'color' => $value['J'],
                            'sku' => $value['N'],
                            'price' => $value['AF'],
                            'stock' => $value['AA'],
                            'image' => $value['R']
                        ]
                    ]
                ];
            }
        }

        // Cập nhật số lượng về 0 cho các variant không có trong file Excel
        if (!empty($current_skus)) {
            Variant::whereNotIn('sku', $current_skus)->update(['stock' => 0]);
        }

        return $object_data;
    }

    public function form_import_excel()
    {
        return view('shop.form_import_excel');
    }

    public function import_excel(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);

        $file = $request->file('file');
        $file->move(public_path('uploads'), 'data_shoes.xlsx');

        return "Nhập thành công";
    }
}
