<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;

class Navbar extends Component
{
    public $products;
    public $search;
    public $brands;
    public $types;

    public $name_customer = "";
    public $phone_customer = "";
    public $address_customer = "";
    public $email_customer = "";

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

            // Lấy thông tin khách hàng từ cookie
            $this->name_customer = session()->get('name_customer');
            $this->phone_customer = session()->get('phone_customer');
            $this->address_customer = session()->get('address_customer');
            $this->email_customer = session()->get('email_customer');


            // dd(session()->all());
        }
    }

    #[On('cart_added')]
    public function add_cart_success()
    {
        $this->cart = session()->get('cart_' . Cookie::get('device_id'), []);
    }

    public function clear_cart()
    {
        // Xoá session giỏ hàng
        session()->forget('cart_' . Cookie::get('device_id'));
        // Clear cookie 
        Cookie::queue(Cookie::forget('device_id'));
        $this->cart = [];

        $this->dispatch('clear_cart');
    }

    public function dat_hang()
    {
        $deviceId = Cookie::get('device_id');

        // Nếu không đủ 4 thông tin thì báo lỗi và không thực hiện đặt hàng
        if (empty($this->name_customer) || empty($this->phone_customer) || empty($this->address_customer) || empty($this->email_customer)) {
            $this->dispatch('dat_hang_error');

            Notification::make()
                ->title('Đặt hàng chưa hợp lệ')
                ->danger()
                ->iconColor('danger')
                ->icon('heroicon-o-x-mark')
                ->duration(3000)
                ->body('Vui lòng chọn lại!')
                ->send();
            return;
        }

        try {
            // Validate để đưa ra thông báo lỗi nếu $this->name_customer không phải là tên hợp lệ
            $this->validate([
                'name_customer' => 'required|string|min:3|max:255',
                'phone_customer' => 'required|string|min:10|max:11',
                'address_customer' => 'required|string|min:3|max:255',
                'email_customer' => 'required|email',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Nếu thông tin không hợp lệ thì báo lỗi và không thực hiện đặt hàng
            $this->dispatch('dat_hang_error');
        
            Notification::make()
                ->title('Thông tin người dùng không hợp lệ')
                ->danger()
                ->iconColor('danger')
                ->icon('heroicon-o-x-mark')
                ->duration(3000)
                ->body('Vui lòng điền thông tin hợp lệ!')
                ->send();
        
            return;
        }

        // Lưu thông tin khách hàng vào cookie
        session()->put('name_customer', $this->name_customer);
        session()->put('phone_customer', $this->phone_customer);
        session()->put('address_customer', $this->address_customer);
        session()->put('email_customer', $this->email_customer);

        // Xoá session giỏ hàng
        session()->forget('cart_' . Cookie::get('device_id'));
        // Clear cookie 
        Cookie::queue(Cookie::forget('device_id'));
        $this->cart = [];

        $this->dispatch('clear_cart_after_dat_hang');

        Notification::make()
            ->title('Đặt hàng thành công!')
            ->success()
            ->iconColor('success')
            ->icon('heroicon-o-check-circle')
            ->duration(3000)
            ->body('Đặt - đặt nữa - đặt mãi!')
            ->send();
    }


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
