<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductImage;
use Livewire\Component;
use Filament\Notifications\Notification;

class ProductImageOrganizer extends Component
{
    public $product;
    public $images;
    
    public function mount($product)
    {
        $this->product = $product;
        $this->refreshImages();
    }
    
    public function refreshImages()
    {
        $this->images = $this->product->productImages()->orderBy('order')->get();
    }
    
    public function render()
    {
        return view('livewire.image-organizer-modal');
    }
    
    public function updateImageOrder(array $imageIds)
    {
        // Cập nhật thứ tự các ảnh dựa trên mảng ID đã sắp xếp
        foreach ($imageIds as $index => $imageId) {
            ProductImage::where('id', $imageId)->update(['order' => $index + 1]);
        }
        
        // Làm mới danh sách ảnh
        $this->refreshImages();
        
        Notification::make()
            ->title('Đã cập nhật thứ tự ảnh')
            ->success()
            ->send();
    }
    
    public function resetImageOrder()
    {
        $images = ProductImage::where('product_id', $this->product->id)
            ->orderBy('id')
            ->get();
            
        foreach ($images as $index => $image) {
            $image->update(['order' => $index + 1]);
        }
        
        // Làm mới danh sách ảnh
        $this->refreshImages();
        
        Notification::make()
            ->title('Đã đặt lại thứ tự ảnh')
            ->success()
            ->send();
    }
}