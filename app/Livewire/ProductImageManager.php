<?php

namespace App\Livewire;

use App\Models\ProductImage;
use App\Models\VariantImage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Collection;

class ProductImageManager extends Component
{
    use WithFileUploads;

    public $productId;
    public Collection $images;
    public Collection $variantImages;
    
    protected $listeners = [
        'refreshImages' => '$refresh',
    ];

    public function mount($productId)
    {
        $this->productId = $productId;
        $this->images = collect();
        $this->variantImages = collect();
        $this->loadImages();
    }

    public function loadImages()
    {
        $this->images = ProductImage::where('product_id', $this->productId)
            ->orderBy('order')
            ->get();

        // Lấy các ảnh variant chưa được thêm vào product
        $this->variantImages = VariantImage::whereHas('variant', function ($query) {
                $query->where('product_id', $this->productId);
            })
            ->whereNotIn('id', $this->images->pluck('variant_image_id')->filter())
            ->get();
    }

    public function uploadMultiple($files)
    {
        foreach ($files as $file) {
            $path = $file->store('products', 'public');
            
            ProductImage::create([
                'product_id' => $this->productId,
                'image' => $path,
                'type' => 'upload'
            ]);
        }

        $this->loadImages();
    }

    public function addFromUrls($urls)
    {
        foreach ($urls as $url) {
            ProductImage::create([
                'product_id' => $this->productId,
                'image' => $url,
                'type' => 'upload',
                'source' => $url
            ]);
        }

        $this->loadImages();
    }

    public function addFromVariantImages($variantImageIds)
    {
        $variantImages = VariantImage::whereIn('id', $variantImageIds)->get();
        
        foreach ($variantImages as $variantImage) {
            ProductImage::create([
                'product_id' => $this->productId,
                'image' => $variantImage->image,
                'type' => 'variant',
                'variant_image_id' => $variantImage->id
            ]);
        }

        $this->loadImages();
    }

    public function reorder($ids)
    {
        ProductImage::updateOrder($ids);
        $this->loadImages();
    }

    public function deleteImage($imageId)
    {
        ProductImage::find($imageId)?->delete();
        $this->loadImages();
    }

    public function render()
    {
        return view('livewire.product-image-manager');
    }
}