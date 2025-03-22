<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use App\Mail\OrderShipped;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Illuminate\Support\Facades\Cookie;

class Checkout extends Component
{
    public $cart = [];
    public $total = 0;
    public $name_customer = '';
    public $phone_customer = '';
    public $email_customer = '';
    public $address_customer = '';
    public $payment_method = 'cod';
    public $accountNumber;
    public $accountHolder;

    public function mount()
    {
        $this->cart = session('cart_' . Cookie::get('device_id'), []);
        
        if (empty($this->cart)) {
            return redirect()->route('shop.store_front')->with('error', 'Giỏ hàng của bạn đang trống');
        }
        
        // Nếu đã có customer_id trong session thì lấy thông tin
        if (session()->has('customer_id')) {
            $customer = Customer::find(session('customer_id'));
            $this->name_customer = $customer->name;
            $this->phone_customer = $customer->phone;
            $this->address_customer = $customer->address;
            $this->email_customer = $customer->email;
        }
        
        $this->total = array_reduce($this->cart, fn($total, $item) => $total + $item['quantity'] * $item['price'], 0);
        $this->accountNumber = config('app.bank_account_number', '');
        $this->accountHolder = config('app.bank_account_holder', '');
    }

    public function dat_hang()
    {
        $this->validate([
            'name_customer' => 'required',
            'phone_customer' => 'required',
            'address_customer' => 'required',
            'payment_method' => 'required|in:cod,bank',
        ], [
            'name_customer.required' => 'Vui lòng nhập tên',
            'phone_customer.required' => 'Vui lòng nhập số điện thoại',
            'address_customer.required' => 'Vui lòng nhập địa chỉ',
        ]);

        try {
            // Tạo hoặc cập nhật thông tin khách hàng
            $customer = Customer::updateOrCreate(
                ['phone' => $this->phone_customer],
                [
                    'name' => $this->name_customer,
                    'email' => $this->email_customer,
                    'address' => $this->address_customer,
                ]
            );

            // Tạo đơn hàng
            $order = Order::create([
                'customer_id' => $customer->id,
                'total' => $this->total,
                'payment_method' => $this->payment_method,
                'status' => 'pending',
            ]);

            // Tạo chi tiết đơn hàng
            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Trừ tồn kho của Variants
            foreach ($this->cart as $cart_item) {
                $variant = Variant::find($cart_item['variant_id']);
                // Nếu trừ mà lớn hơn hoặc bằng 0 thì trừ tồn kho
                if ($variant->stock - $cart_item['quantity'] >= 0) {
                    $variant->stock -= $cart_item['quantity'];
                    $variant->save();
                }
            }

            // Gửi email thông báo
            Mail::to('thanshoes99@gmail.com')->send(new OrderShipped($order));
            Mail::to('tranmanhhieu10@gmail.com')->send(new OrderShipped($order));

            // Xóa giỏ hàng
            session()->forget('cart_' . Cookie::get('device_id'));

            // Lưu customer_id vào session
            session()->put('customer_id', $customer->id);

            // Gửi sự kiện clear giỏ hàng
            $this->dispatch('clear_cart_after_dat_hang');
            
            session()->flash('message', 'Đặt hàng thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.');
            return redirect()->route('shop.store_front');

        } catch (\Exception $e) {
            session()->flash('error', 'Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại sau: ' . $e->getMessage());
            return redirect()->route('shop.store_front');
        }
    }

    public function render()
    {
        return view('livewire.checkout');
    }
}