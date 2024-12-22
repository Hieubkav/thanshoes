<?php

namespace App\Livewire;

use Livewire\Component;

class ProductOverview extends Component
{
    public $product;
    public $selectedColor = [];
    public $selectedSize = [];
    public $countfilter = 1;
    public $clicked = false;

    // Nhận dữ liệu từ component cha
    public function mount($product)
    {
        $this->product = $product;
        // Kiểm tra xem sản phẩm có phân loại theo color không
        if($this->product->variants->where('color', '!=', null)->count() > 0){
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

    // Xử lý khi người dùng chọn size
    public function updatingSelectedSize($value)
    {
        if ($this->clicked==false and $this->countfilter==2) {
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
            $list_images_variants = $this->product->variants->map(function($variant){
                return $variant->variant_images;
            })->flatten();

            // Lấy ra link ảnh của các biến thể
            $list_link_images_variants = $list_images_variants->map(function($image){
                return $image->image;
            });

            // Loại bỏ link trùng hoặc link rỗng
            $list_link_images_variants = $list_link_images_variants->unique()->filter(function($image){
                return $image != null;
            });
        // Lấy ra ảnh chính của sản phẩm

        $main_image = $list_link_images_variants->first();
        $list_colors = $this->product->variants->map(function($variant){
            return $variant->color;
        })->unique()->filter(function($color){
            return $color != null;
        });

        $list_sizes = $this->product->variants->map(function($variant){
            return $variant->size;
        })->unique()->filter(function($size){
            return $size != null;
        });


        return view(
            'livewire.product-overview',
            [
                'list_images_variants' => $list_link_images_variants,
                'main_image' => $main_image,
                'product' => $this->product,
                'list_colors' => $list_colors,
                'list_sizes' => $list_sizes
            ]
        );
    }
}
