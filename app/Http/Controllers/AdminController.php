<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
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

        // Kiểm tra xem file có tồn tại không
        if (!file_exists($filePath)) {
            return "File Excel không tồn tại. Vui lòng tải lên file trước.";
        }

        // Load file excel
        $spreadsheet = IOFactory::load($filePath);

        // Lấy ra dữ liệu của sheet đầu tiên
        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Mảng lưu trữ SKU hiện tại để kiểm tra sau
        $current_skus = [];

        // Mảng lưu kết quả để trả về
        $object_data = [];

        // Lấy header row để hiểu cấu trúc (dòng đầu tiên)
        $header = !empty($data[1]) ? $data[1] : [];

        // Bắt đầu từ dòng thứ 2 (index 2) vì dòng 1 là header
        foreach ($data as $key => $value) {
            if ($key >= 2 && !empty($value['A'])) {  // Bỏ qua header và dòng trống
                $product_name = $value['A']; // Tên sản phẩm
                $sku = $value['N']; // Mã SKU

                if (empty($sku)) {
                    continue; // Bỏ qua các dòng không có SKU
                }

                // Thêm SKU vào mảng kiểm tra
                $current_skus[] = $sku;

                // Tách SKU để lấy mã sản phẩm (phần trước dấu -)
                $sku_parts = explode('-', $sku);
                $product_code = $sku_parts[0];

                // Tìm sản phẩm dựa vào pattern của SKU
                $existing_variant = Variant::where('sku', 'LIKE', $product_code . '-%')->first();

                if ($existing_variant) {
                    $product = $existing_variant->product;
                } else {
                    // Tạo sản phẩm mới nếu không tìm thấy SKU pattern
                    $product = new Product();
                }

                // Cập nhật thông tin sản phẩm
                $product->name = $product_name;
                $product->description = $value['D']; // Mô tả sản phẩm
                $product->brand = $value['E']; // Nhãn hiệu
                $product->type = $value['C']; // Loại sản phẩm
                $product->save();

                // Xử lý biến thể
                $variant = Variant::where('sku', $sku)->first();
                if (!$variant) {
                    $variant = new Variant();
                    $variant->product_id = $product->id;
                    $variant->sku = $sku;
                }

                // Cập nhật thông tin biến thể
                $variant->color = $value['J'] ?? ''; // Giá trị thuộc tính 2 (màu sắc)
                $variant->size = $value['H'] ?? ''; // Giá trị thuộc tính 1 (kích thước)

                // Xử lý giá, đảm bảo giá trị hợp lệ
                $price = $value['AF'] ?? 0; // PL_Giá bán lẻ
                if (is_string($price)) {
                    $price = str_replace([',', '.'], '', $price);
                }
                $variant->price = (int)$price;

                // Xử lý số lượng tồn kho
                $stock = $value['AA'] ?? 0; // LC_CN1_Tồn kho ban đầu
                if (is_string($stock)) {
                    $stock = str_replace([',', '.'], '', $stock);
                }
                $variant->stock = (int)$stock;

                $variant->save();

                // Thêm ảnh mới cho variant nếu có
                if (!empty($value['R'])) { // Ảnh đại diện
                    // Kiểm tra xem đã có ảnh cho variant này chưa
                    $existing_image = VariantImage::where('variant_id', $variant->id)
                        ->where('image', $value['R'])
                        ->first();

                    if (!$existing_image) {
                        $variant_image = new VariantImage();
                        $variant_image->variant_id = $variant->id;
                        $variant_image->image = $value['R'];
                        $variant_image->save();
                    }
                }

                // Thêm vào mảng kết quả
                $object_data[] = [
                    'name' => $product_name,
                    'type' => $value['C'],
                    'description' => $value['D'],
                    'brand' => $value['E'],
                    'tags' => $value['F'] ?? '',
                    'variants' => [
                        [
                            'size' => $value['H'] ?? '',
                            'color' => $value['J'] ?? '',
                            'sku' => $sku,
                            'price' => $price,
                            'stock' => $stock,
                            'image' => $value['R'] ?? ''
                        ]
                    ]
                ];
            }
        }

        // Cập nhật số lượng về 0 cho các variant không có trong file Excel
        if (!empty($current_skus)) {
            Variant::whereNotIn('sku', $current_skus)->update(['stock' => 0]);
        }

        return view('excel', compact('object_data'));
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

        // Sau khi lưu file, gọi ngay hàm xử lý Excel
        $filePath = public_path('uploads/data_shoes.xlsx');

        try {
            // Load file excel
            $spreadsheet = IOFactory::load($filePath);

            // Lấy ra dữ liệu của sheet đầu tiên
            $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // Mảng lưu trữ SKU hiện tại để kiểm tra sau
            $current_skus = [];
            $processed_count = 0;
            $product_count = 0;

            // Biến để theo dõi sản phẩm hiện tại
            $currentProduct = null;
            $currentProductName = null;
            $currentProductSku = null;

            // Bắt đầu từ dòng thứ 2 (index 2) vì dòng 1 là header
            foreach ($data as $key => $value) {
                if ($key >= 2) {
                    $hasSku = !empty($value['N']);

                    // Nếu có tên sản phẩm mới, tạo hoặc cập nhật sản phẩm mới
                    if (!empty($value['A'])) {
                        $currentProductName = $value['A']; // Lưu tên sản phẩm hiện tại
                        $currentProductSku = null; // Reset SKU sản phẩm

                        // Nếu dòng này cũng có SKU, lấy SKU gốc (phần trước dấu -)
                        if ($hasSku) {
                            $skuParts = explode('-', $value['N']);
                            $currentProductSku = $skuParts[0];
                        }

                        // Tìm sản phẩm dựa vào SKU pattern
                        $product = null;
                        if ($currentProductSku) {
                            // Tìm sản phẩm dựa vào SKU gốc
                            $product = Product::where('sku', $currentProductSku)->first();
                        }

                        if (!$product) {
                            $product = new Product();
                            $product_count++;
                        }

                        $product->name = $currentProductName;
                        $product->description = $value['D'] ?? null;
                        $product->brand = $value['E'] ?? null;
                        $product->type = $value['C'] ?? null;
                        $product->sku = $currentProductSku; // Lưu SKU cho sản phẩm
                        $product->save();

                        $currentProduct = $product;
                    }
                    // Nếu không có tên sản phẩm nhưng có SKU và chưa có SKU sản phẩm
                    else if ($hasSku && !$currentProductSku && $currentProduct) {
                        $skuParts = explode('-', $value['N']);
                        $currentProductSku = $skuParts[0];
                    }

                    // Bỏ qua nếu không có SKU hoặc không có sản phẩm hiện tại
                    if (!$hasSku || !$currentProduct) {
                        continue;
                    }

                    $sku = $value['N'];
                    $current_skus[] = $sku;

                    // Xử lý biến thể
                    $variant = Variant::where('sku', $sku)->first();
                    if (!$variant) {
                        $variant = new Variant();
                        $variant->product_id = $currentProduct->id;
                        $variant->sku = $sku;
                    }

                    // Cập nhật thông tin biến thể
                    $variant->color = $value['J'] ?? ''; // Giá trị thuộc tính 2 (màu sắc)
                    $variant->size = $value['H'] ?? ''; // Giá trị thuộc tính 1 (kích thước)

                    // Xử lý giá, đảm bảo giá trị hợp lệ
                    $price = $value['AF'] ?? 0; // PL_Giá bán lẻ
                    if (is_string($price)) {
                        $price = str_replace([',', '.'], '', $price);
                    }
                    $variant->price = (int)$price;

                    // Xử lý số lượng tồn kho
                    $stock = $value['AA'] ?? 0; // LC_CN1_Tồn kho ban đầu
                    if (is_string($stock)) {
                        $stock = str_replace([',', '.'], '', $stock);
                    }
                    $variant->stock = (int)$stock;

                    $variant->save();

                    // Thêm ảnh mới cho variant nếu có
                    if (!empty($value['R'])) { // Ảnh đại diện
                        $imageUrl = $value['R'];

                        // Kiểm tra xem đã có ảnh cho variant này chưa
                        $existing_image = VariantImage::where('variant_id', $variant->id)
                            ->where('image', $imageUrl)
                            ->first();

                        $variantImage = $existing_image;
                        if (!$existing_image) {
                            $variantImage = new VariantImage();
                            $variantImage->variant_id = $variant->id;
                            $variantImage->image = $imageUrl;
                            $variantImage->save();
                        }

                        // Thêm hoặc cập nhật ProductImage liên quan
                        $existing_product_image = ProductImage::where('product_id', $currentProduct->id)
                            ->where('image', $imageUrl)
                            ->where('type', 'variant')
                            ->first();

                        if (!$existing_product_image) {
                            // Lấy order cao nhất hiện tại
                            $maxOrder = ProductImage::where('product_id', $currentProduct->id)->max('order') ?? 0;

                            $productImage = new ProductImage();
                            $productImage->product_id = $currentProduct->id;
                            $productImage->image = $imageUrl;
                            $productImage->type = 'variant';
                            $productImage->variant_image_id = $variantImage->id;
                            $productImage->source = $imageUrl;
                            $productImage->order = $maxOrder + 1;
                            $productImage->save();
                        }
                    }

                    $processed_count++;
                }
            }

            // Đếm số sản phẩm đã xử lý (dựa trên currentProductSku)
            $uniqueProducts = Product::whereHas('variants', function ($query) use ($current_skus) {
                $query->whereIn('sku', $current_skus);
            })->count();
            // Cập nhật số lượng về 0 cho các variant không có trong file Excel
            if (!empty($current_skus)) {
                $updated_variants = Variant::whereNotIn('sku', $current_skus)->update(['stock' => 0]);
                return "Nhập thành công! Đã xử lý $uniqueProducts sản phẩm với $processed_count biến thể." .
                    "\nĐã cập nhật $updated_variants variant không có trong file về số lượng 0.";
            }

            return "Nhập thành công! Đã xử lý $uniqueProducts sản phẩm với $processed_count biến thể.";
        } catch (\Exception $e) {
            return "Lỗi khi xử lý file Excel: " . $e->getMessage() . " tại dòng " . $e->getLine();
        }
    }
}
