<?php

namespace App\Livewire;

use App\Models\Variant;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

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
    public $cart = [];


    // Nhận dữ liệu từ component cha
    public function mount($product)
    {
        $this->product = $product;
        // Kiểm tra xem sản phẩm có phân loại theo color không
        if ($this->product->variants->where('color', '!=', null)->count() > 0) {
            $this->countfilter = 2;
        }

        // Kiểm tra xem cookie `device_id` có tồn tại không
        if (!Cookie::has('device_id')) {
            // Nếu chưa có, tạo UUID mới cho thiết bị và lưu vào cookie
            $deviceId = (string) Str::uuid();
            Cookie::queue('device_id', $deviceId, 60 * 24 * 365); // Lưu cookie trong 1 năm
        } else {
            // Lấy giỏ hàng từ cookie
            $this->cart = session()->get('cart_' . Cookie::get('device_id'), []);
        }
    }

    #[On('clear_cart')]
    public function clear_cart_sucess()
    {
        // Xoá session giỏ hàng
        session()->forget('cart_' . Cookie::get('device_id'));
        // Clear cookie 
        Cookie::queue(Cookie::forget('device_id'));
        $this->cart = [];

        Notification::make()
            ->title('Đã xoá giỏ hàng')
            ->danger()
            ->iconColor('danger')
            ->icon('heroicon-o-x-mark')
            ->duration(3000)
            ->body('Thêm sản phẩm vào giỏ hàng để mua hàng!')
            ->send()
        ;
    }

    #[On('clear_cart_after_dat_hang')]
    public function clear_cart_after_dat_hang()
    {
        // Xoá session giỏ hàng
        session()->forget('cart_' . Cookie::get('device_id'));
        $this->cart = [];
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

        if (
            ($this->countfilter == 1 and $this->selectedSize == []) or
            ($this->countfilter == 2 and $this->selectedColor == []) or
            ($this->countfilter == 2 and $this->selectedSize == [])
        ) {

            Notification::make()
                ->title('Chưa chọn phân loại sản phẩm')
                ->danger()
                ->iconColor('danger')
                ->icon('heroicon-o-x-mark')
                ->duration(3000)
                ->body('Vui lòng chọn!')
                ->send()
            ;
        } else {
            // Lấy ra device_id từ cookie
            $deviceId = Cookie::get('device_id');

            // Truy xuất thông tin sản phẩm từ sản phẩm đã chọn
            $variant = Variant::where('product_id', $this->product->id)
                ->where('color', $this->selectedColor)
                ->where('size', $this->selectedSize)
                ->first();

            // Kiểm tra xem người dùng có thêm nhiều hơn số lượng hàng tồn kho không
            if (isset($this->cart[$variant->id]) and $variant->stock == $this->cart[$variant->id]['quantity']) {
                Notification::make()
                    ->title('Không thể thêm vào giỏ hàng vượt quá tồn kho ')
                    ->danger()
                    ->iconColor('danger')
                    ->icon('heroicon-o-x-mark')
                    ->duration(3000)
                    ->body('Vui lòng chọn sản phẩm khác!')
                    ->send();

                return;
            }

            // Thêm sản phẩm vào giỏ hàng
            $this->cart[$variant->id] = [
                'product_name' => $variant->product->name,
                'variant_color' => $variant->color,
                'variant_size' => $variant->size,
                'price' => $variant->price,
                'quantity' => isset($this->cart[$variant->id])
                    ? $this->cart[$variant->id]['quantity'] + 1
                    : 1,
                'image' => $variant->variant_images->first()->image,
            ];

            // Lưu giỏ hàng vào session dựa trên device_id
            session()->put('cart_' . $deviceId, $this->cart);

            //Gửi thông báo khi thêm vào giỏ hàng thành công
            Notification::make()
                ->title('Thêm giỏ hàng thành công')
                ->success()
                ->duration(3000)
                ->body('Chúc mừng bạn')
                ->send();

            $this->dispatch('cart_added');
        }
    }

    // public function addCart($variant_id, $quantity = 1)
    // {
    //     $deviceId = Cookie::get('device_id'); // Lấy device_id từ cookie

    //     // Truy xuất thông tin sản phẩm từ variant_id
    //     $variant = Variant::find($variant_id); // Variant là model của bạn

    //     if (!$variant) {
    //         $this->dispatch('variant_cant_find');
    //         return;
    //     }

    //     // Thêm sản phẩm vào giỏ hàng
    //     $this->cart[$variant_id] = [
    //         'product_name' => $variant->product->name,
    //         'variant_color' => $variant->color,
    //         'variant_size' => $variant->size,
    //         'price' => $variant->price,
    //         'quantity' => isset($this->cart[$variant_id])
    //             ? $this->cart[$variant_id]['quantity'] + $quantity
    //             : $quantity,
    //     ];

    //     // Lưu giỏ hàng vào session dựa trên device_id
    //     session()->put('cart_' . $deviceId, $this->cart);

    //     // Thông báo
    //     $this->dispatch('cart_added');
    // }


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
