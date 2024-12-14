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
        $filePath = public_path('uploads/data_shoes.xlsx');

        // Xoá toàn bộ dữ liệu trong bảng Product và Variant và VarientImage
        DB::table('products')->delete();
        DB::table('variants')->delete();
        DB::table('variant_images')->delete();

        $spreadsheet = IOFactory::load($filePath);

        // Lấy ra dữ liệu của sheet đầu tiên chỉ lấy cột A 
        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Tạo ra một đối tượng object trong laravel là một mảng có object mỗi object trông đó có các trường như : Tên sản phẩm, loại sản phẩm, mô tả,nhãn hiệu, tags, một mảng object (trong mảng object này có các trường như: size, màu sắc, tên phiên bản, SKU, Barcode, khối lượng, đơn vị tính, giá bán lẻ, ảnh đại diện)
        $object_data = [];

        $process_data = [];
        foreach ($data as $key => $value) {
            if ($value['A'] != '' && $key != 1) {
                // Lưu dữ liệu vào bảng Product
                $product = new Product();
                $product->name = $value['A'];
                $product->description = $value['D'];
                $product->brand = $value['E'];
                $product->type = $value['C'];
                $product->save();


                // Xử lý tạo ra varient
                $start = $key;
                $end = $start;
                while ($end < count($data) && $data[$end + 1]['A'] == '') {
                    $end++;
                }
                $variant = [];
                for ($i = $start; $i < $end; $i++) {
                    $variant[] = [
                        'size' => $value['H'],
                        'color' => $value['J'],
                        'variant_name' => $value['M'],
                        'sku' => $value['N'],
                        'barcode' => $value['O'],
                        'weight' => $value['P'],
                        'unit' => $value['Q'],
                        'price' => $value['AF'],
                        'image' => $value['R'],
                        'quantity' => $value['AA'],
                    ];

                    // Lưu dữ liệu vào bảng Variant
                    $variant_data = new Variant();
                    $variant_data->color = $value['J'];
                    $variant_data->size = $value['H'];
                    $variant_data->price = str_replace(',', '', $value['AF']);
                    $variant_data->stock =str_replace(',', '', $value['AA']);
                    $variant_data->product_id = $product->id;
                    $variant_data->save();

                    // Lưu dữ liệu vào bảng VariantImage
                    $variant_image = new VariantImage();
                    $variant_image->variant_id = $variant_data->id;
                    $variant_image->image = $value['R'];
                    $variant_image->save();
                }

                $object_data[] = [
                    'name' => $value['A'],
                    'type' => $value['C'],
                    'description' => $value['D'],
                    'brand' => $value['E'],
                    'tags' => $value['F'],
                    'variants' => $variant
                ];

                
            }
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

    public function test()
    {
        $data = [];

        for ($i = 0; $i < 10; $i++) {
            $data[] = $i;
        }

        return view('test', compact('data'));
    }
}
