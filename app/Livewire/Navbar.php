<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Component;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderShipped;

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

    public $payment_method = "cod";
    public $customer_id = null;

    public $cart = [];

    // mặc địnhh $order bằng một collection rỗng
    public $order;

    //*********** Cấu hình qrcode
    public $accountNumber = '0946775145';
    public $accountHolder = 'Nguyễn Nhật Tân';
    public $amount = 1000000; // Số tiền chuyển
    public $content = 'Thanh toán hóa đơn'; // Nội dung chuyển khoản
    //********** end qr code

    // Hàm khởi tạo
    public function mount()
    {
        $this->order = new Collection();

        // Kiểm tra xem cookie `device_id` có tồn tại không
        if (!Cookie::has('device_id')) {
            // Nếu chưa có, tạo UUID mới cho thiết bị và lưu vào cookie
            $deviceId = (string) Str::uuid();
            Cookie::queue('device_id', $deviceId, 60 * 24 * 365); // Lưu cookie trong 1 năm
        } else {
            // Lấy giỏ hàng từ cookie
            $this->cart = session()->get('cart_' . Cookie::get('device_id'), []);

            // Nếu đã tồn tại session customer_id thì lấy thông tin khách hàng từ database
            if (session()->has('customer_id'))  {
                $this->customer_id = session()->get('customer_id');
                // Lấy thông tin khách hàng từ cookie
                $this->name_customer = Customer::find($this->customer_id)->name;
                $this->phone_customer = Customer::find($this->customer_id)->phone;
                $this->address_customer = Customer::find($this->customer_id)->address;
                $this->email_customer =  Customer::find($this->customer_id)->email;

                $this->order = Order::where('customer_id', $this->customer_id)->latest()->get();
            }

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
        if (empty($this->name_customer) || empty($this->phone_customer) || empty($this->address_customer) ) {
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
                'email_customer' => 'max:255',
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

        // Kiểm tra số điện thoại có tồn tại trong bảng customer không, nếu không thì tạo ra customer mới
        if (Customer::where('phone', $this->phone_customer)->count() == 0) {
            $customer = Customer::create([
                'name' => $this->name_customer,
                'phone' => $this->phone_customer,
                'address' => $this->address_customer,
                'email' => $this->email_customer,
            ]);
            $this->customer_id = $customer->id;
            session()->put('customer_id', $this->customer_id);
        } else {
            $this->customer_id = Customer::where('phone', $this->phone_customer)->first()->id;
            session()->put('customer_id', $this->customer_id);
        }

        // Lưu lại  thông  tin đơn hàng vừa đặt hàng
        $new_order = Order::create([
            'status' => 'pending',
            'customer_id' => $this->customer_id,
            'payment_method' => $this->payment_method,
        ]);
        // Lưu lại chi tiết đơn hàng vào order_items
        foreach ($this->cart as $cart_item) {
            OrderItem::create([
                'price' => $cart_item['price'],
                'quantity' => $cart_item['quantity'],
                'order_id' => $new_order->id,
                'variant_id' => $cart_item['variant_id'],
            ]);
        }

        // Trừ tồn kho của Variants
        foreach ($this->cart as $cart_item) {
            $variant = Variant::find($cart_item['variant_id']);
            $variant->decrement('stock', $cart_item['quantity']);
        }

        // Gửi email cho chủ shop thanshoes99@gmail.com
        Mail::to('thanshoes99@gmail.com')->send(new OrderShipped($new_order));
        Mail::to('tranmanhhieu10@gmail.com')->send(new OrderShipped($new_order));
        //

        // Cho  session giỏ hàng rỗng
        session()->forget('cart_' . $deviceId);
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
