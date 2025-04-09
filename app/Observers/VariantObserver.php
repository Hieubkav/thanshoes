<?php

namespace App\Observers;

use App\Models\Variant;
use App\Models\ProductImage;

class VariantObserver
{
    public function created(Variant $variant): void
    {
        // Tự động thêm ảnh từ variant vào product_images
        if (!$variant->images || !$variant->images->count()) {
            return;
        }

        foreach ($variant->images as $variantImage) {
            ProductImage::create([
                'product_id' => $variant->product_id,
                'image' => $variantImage->image,
                'type' => 'variant',
                'variant_image_id' => $variantImage->id,
            ]);
        }
    }

    public function deleted(Variant $variant): void
    {
        // Xóa các product_images liên kết với variant bị xóa
        if ($variant->images) {
            ProductImage::where('type', 'variant')
                ->whereIn('variant_image_id', $variant->images->pluck('id'))
                ->delete();
        }
    }
}