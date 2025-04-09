<?php

namespace App\Forms\Components;

use App\Models\ProductImage;
use App\Models\VariantImage;
use Filament\Forms\Components\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProductImageUploader extends Field
{
    protected string $view = 'forms.components.product-image-uploader';

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(false);
    }

    public function getState(): mixed
    {
        $record = $this->getRecord();
        
        if (!$record instanceof Model) {
            return collect();
        }

        return $record->images()
            ->with('variantImage')
            ->ordered()
            ->get();
    }

    // Lấy tất cả ảnh từ variant_images của sản phẩm 
    public function getVariantImages(): Collection
    {
        $record = $this->getRecord();
        
        if (!$record) {
            return collect();
        }

        $existingVariantImageIds = $record->images()
            ->whereNotNull('variant_image_id')
            ->pluck('variant_image_id');

        return VariantImage::whereIn('variant_id', $record->variants()->pluck('id'))
            ->whereNotIn('id', $existingVariantImageIds)
            ->get();
    }

    // Thêm ảnh từ variant_images
    public function addFromVariantImages(array $variantImageIds): void
    {
        $record = $this->getRecord();
        
        if (!$record) {
            return;
        }

        $variantImages = VariantImage::whereIn('id', $variantImageIds)->get();

        foreach ($variantImages as $variantImage) {
            ProductImage::create([
                'product_id' => $record->id,
                'image' => $variantImage->image,
                'type' => 'variant',
                'variant_image_id' => $variantImage->id,
            ]);
        }
    }

    // Upload nhiều ảnh
    public function uploadMultiple(array $files): void
    {
        $record = $this->getRecord();
        
        if (!$record) {
            return;
        }

        foreach ($files as $file) {
            $path = $file->store('products', 'public');
            
            ProductImage::create([
                'product_id' => $record->id,
                'image' => $path,
                'type' => 'upload'
            ]);
        }
    }

    // Thêm ảnh từ URL
    public function addFromUrls(array $urls): void
    {
        $record = $this->getRecord();
        
        if (!$record) {
            return;
        }

        foreach ($urls as $url) {
            ProductImage::create([
                'product_id' => $record->id,
                'image' => $url,
                'type' => 'upload',
                'source' => $url
            ]);
        }
    }

    // Cập nhật thứ tự ảnh
    public function reorder(array $ids): void
    {
        ProductImage::updateOrder($ids);
    }

    // Xóa ảnh
    public function deleteImage(int $imageId): void
    {
        ProductImage::find($imageId)?->delete();
    }
}