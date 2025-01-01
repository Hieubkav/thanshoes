<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class Navbar extends Component
{
    public $products;
    public $search;
    public $brands;
    public $types;

    public $cart = [];

    // Hàm khởi tạo
    public function mount()
    {
        // Kiểm tra xem cookie `device_id` có tồn tại không
        if (!Cookie::has('device_id')) {
            // Nếu chưa có, tạo UUID mới cho thiết bị và lưu vào cookie
            $deviceId = (string) Str::uuid();
            Cookie::queue('device_id', $deviceId, 60 * 24 * 365); // Lưu cookie trong 1 năm
        } else {
            // Lấy giỏ hàng từ cookie
            $this->cart = session()->get('cart_' . Cookie::get('device_id'), []);
            // dd($this->cart);
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


    public function render()
    {
        $this->products = Product::all();


        // Lấy ra danh sách những thuộc tính khác nhau có thể có của product->brand trừ rỗng
        $this->brands = $this->products->pluck('brand')->filter()->unique();

        // lấy ra danh sách những bảng ghi khác nhau có thể có của product->type
        $this->types = $this->products->pluck('type')->filter()->unique();
        return view('livewire.navbar');
    }
}
