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
                        'size' => $data[$i]['H'],
                        'color' => $data[$i]['J'],
                        'variant_name' => $data[$i]['A'],
                        'sku' =>  $data[$i]['N'],
                        'barcode' => $data[$i]['O'],
                        'weight' => $data[$i]['P'],
                        'unit' => $data[$i]['Q'],
                        'price' => $data[$i]['AF'],
                        'image' => $data[$i]['R'],
                        'quantity' => $data[$i]['AA'],
                    ];

                    // Lưu dữ liệu vào bảng Variant
                    $variant_data = new Variant();
                    $variant_data->color = $data[$i]['J'];
                    $variant_data->size = $data[$i]['H'];
                    $variant_data->price = str_replace(',', '', $data[$i]['AF']);
                    $variant_data->stock = str_replace(',', '', $data[$i]['AA']);
                    $variant_data->product_id = $product->id;
                    $variant_data->save();

                    // Lưu dữ liệu vào bảng VariantImage
                    $variant_image = new VariantImage();
                    $variant_image->variant_id = $variant_data->id;
                    $variant_image->image = $data[$i]['R'];
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

    public function phan_trang(Request $request)
    {
        // Lấy dữ liệu sản phẩm và phân trang
        $products = Product::paginate(5);

        if ($request->ajax()) {
            // Trả về dữ liệu JSON gồm danh sách sản phẩm và phân trang
            return response()->json([
                'products' => view('partials.product_list_phan_trang', compact('products'))->render(),
                'pagination' => (string) $products->links('pagination::tailwind')
            ]);
        }

        // Trả về view bình thường nếu không phải yêu cầu AJAX
        return view('shop.phan_trang', compact('products'));
    }
}
