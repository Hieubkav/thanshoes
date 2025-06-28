<?php

namespace App\Actions;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Variant;
use App\Models\VariantImage;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportExcelAction
{
    use AsAction;

    public function handle(Request $request): string
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

                        // Xử lý tags nếu có trong cột F
                        if (!empty($value['F'])) {
                            $this->processTags($product, $value['F']);
                        }

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
                    $variant = $this->processVariant($sku, $currentProduct, $value);
                    
                    // Thêm ảnh mới cho variant nếu có
                    if (!empty($value['R'])) {
                        $this->processVariantImage($variant, $currentProduct, $value['R']);
                    }

                    $processed_count++;
                }
            }

            // Đếm số sản phẩm đã xử lý
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

    private function processTags(Product $product, string $tagsString): void
    {
        $tagNames = array_map('trim', explode(',', $tagsString));
        $tagIds = [];
        
        foreach ($tagNames as $tagName) {
            if (!empty($tagName)) {
                // Tìm tag hiện có hoặc tạo mới
                $tag = \App\Models\Tag::where('name', $tagName)->first();

                if (!$tag) {
                    $tag = new \App\Models\Tag();
                    $tag->name = $tagName;
                    $tag->save();
                }

                $tagIds[] = $tag->id;
            }
        }

        // Đồng bộ tags với sản phẩm
        $product->tags()->sync($tagIds);
    }

    private function processVariant(string $sku, Product $currentProduct, array $value): Variant
    {
        $variant = Variant::where('sku', $sku)->first();
        if (!$variant) {
            $variant = new Variant();
            $variant->product_id = $currentProduct->id;
            $variant->sku = $sku;
        }

        // Cập nhật thông tin biến thể
        $variant->color = $value['J'] ?? '';
        $variant->size = $value['H'] ?? '';

        // Xử lý giá
        $price = $value['AF'] ?? 0;
        if (is_string($price)) {
            $price = str_replace([',', '.'], '', $price);
        }
        $variant->price = (int)$price;

        // Xử lý số lượng tồn kho
        $stock = $value['AA'] ?? 0;
        if (is_string($stock)) {
            $stock = str_replace([',', '.'], '', $stock);
        }
        $variant->stock = (int)$stock;

        $variant->save();
        return $variant;
    }

    private function processVariantImage(Variant $variant, Product $currentProduct, string $imageUrl): void
    {
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
}
