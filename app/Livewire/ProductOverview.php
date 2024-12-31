<?php

namespace App\Livewire;

use Filament\Notifications\Notification;
use Livewire\Component;

class ProductOverview extends Component
{
    // $product là biến chứa dữ liệu sản phẩm được truyền từ component cha
    public $product;
    // $selectedColor chứa màu được chọn
    public $selectedColor = [];
    // $selectedSize chứa size được chọn
    public $selectedSize = [];
    // $countfilter chứa số lượng filter
    public $countfilter = 1;
    // $clicked chứa trạng thái người dùng đã chọn màu chưa
    public $clicked = false;
    // $main_image chứa ảnh chính của sản phẩm
    public $main_image;


    // Nhận dữ liệu từ component cha
    public function mount($product)
    {
        $this->product = $product;
        // Kiểm tra xem sản phẩm có phân loại theo color không
        if ($this->product->variants->where('color', '!=', null)->count() > 0) {
            $this->countfilter = 2;
        }
    }


    // Xử lý khi người dùng chọn màu
    public function updatingSelectedColor($color)
    {
        $this->clicked = true;
        $this->selectedSize = [];
        $this->dispatch('colorSelected', $color);
    }

    public function addToCart()
    {

        if ($this->countfilter == 1 and $this->selectedSize == []) {
            Notification::make()
                ->title('Chưa chọn phiên bản')
                ->danger()
                ->iconColor('danger')
                ->icon('heroicon-o-x-mark')
                ->duration(2000)
                ->body('Vui lòng chọn!')
                ->send();
        } else {
            Notification::make()
                ->title('Thêm giỏ hàng thành công')
                ->success()
                ->duration(2000)
                ->body('Chúc mừng bạn')
                ->send();
        }
    }

    // Xử lý khi người dùng chọn size
    public function updatingSelectedSize($value)
    {
        if ($this->clicked == false and $this->countfilter == 2) {
            $this->dispatch('checkcolorfirst');
        } else {
            $this->clicked = true;
            $this->dispatch('sizeSelected', $value);
        }
    }

    public function render()
    {
        //
        // Lấy ra tất cả ảnh của các biến thể của sản phẩm
        $list_images_variants = $this->product->variants->map(function ($variant) {
            return $variant->variant_images;
        })->flatten();

        // Lấy ra link ảnh của các biến thể
        $list_link_images_variants = $list_images_variants->map(function ($image) {
            return $image->image;
        });

        // Loại bỏ link trùng hoặc link rỗng
        $list_link_images_variants = $list_link_images_variants->unique()->filter(function ($image) {
            return $image != null;
        });
        // Lấy ra ảnh chính của sản phẩm

        $this->main_image = $list_link_images_variants->first();
        $list_colors = $this->product->variants->map(function ($variant) {
            return $variant->color;
        })->unique()->filter(function ($color) {
            return $color != null;
        });

        $list_sizes = $this->product->variants->map(function ($variant) {
            return $variant->size;
        })->unique()->filter(function ($size) {
            return $size != null;
        });


        return view(
            'livewire.product-overview',
            [
                'list_images_variants' => $list_link_images_variants,
                'main_image' => $this->main_image,
                'product' => $this->product,
                'list_colors' => $list_colors,
                'list_sizes' => $list_sizes
            ]
        );
    }
}
