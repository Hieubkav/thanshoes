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
use Filament\Notifications\Notification;

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
    public $bankCode;

    public function mount()
    {
        $this->payment_method = 'cod';
        $this->cart = session('cart_' . Cookie::get('device_id'), []);
        
        if (empty($this->cart)) {
            return redirect()->route('shop.store_front')
                ->with('error', 'Giỏ hàng của bạn đang trống');
        }
        
        if (session()->has('customer_id')) {
            $customer = Customer::find(session('customer_id'));
            if ($customer) {
                $this->name_customer = $customer->name;
                $this->phone_customer = $customer->phone;
                $this->address_customer = $customer->address;
                $this->email_customer = $customer->email;
            }
        }
        
        $this->total = array_reduce($this->cart, fn($total, $item) => $total + $item['quantity'] * $item['price'], 0);
        $this->accountNumber = '0946775145';
        $this->accountHolder = 'NGUYEN NHAT TAN';
        $this->bankCode = 'MB';
    }

    public function updatedPaymentMethod($value)
    {
        if (!in_array($value, ['cod', 'bank'])) {
            $this->payment_method = 'cod';
            return;
        }

        $this->dispatch('payment-method-updated', $value);
    }

    protected function validateInfo()
    {
        $errors = [];

        if (empty($this->name_customer)) {
            $errors[] = 'Vui lòng nhập họ tên';
        }

        if (empty($this->phone_customer)) {
            $errors[] = 'Vui lòng nhập số điện thoại';
        }

        if (empty($this->address_customer)) {
            $errors[] = 'Vui lòng nhập địa chỉ';
        }

        if (!empty($this->email_customer) && !filter_var($this->email_customer, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không đúng định dạng';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                Notification::make()
                    ->title('Thiếu thông tin')
                    ->body($error)
                    ->danger()
                    ->send();
            }
            return false;
        }

        return true;
    }

    public function dat_hang()
    {
        if (!$this->validateInfo()) {
            return;
        }

        try {
            $customer = Customer::updateOrCreate(
                ['phone' => $this->phone_customer],
                [
                    'name' => $this->name_customer,
                    'email' => $this->email_customer,
                    'address' => $this->address_customer
                ]
            );

            $order = Order::create([
                'customer_id' => $customer->id,
                'total' => $this->total,
                'payment_method' => $this->payment_method,
                'status' => 'pending'
            ]);

            foreach ($this->cart as $item) {
                $variant = Variant::find($item['variant_id']);
                
                if (!$variant || $variant->stock < $item['quantity']) {
                    throw new \Exception('Sản phẩm ' . $item['product_name'] . ' không đủ số lượng trong kho');
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);

                $variant->stock -= $item['quantity'];
                $variant->save();
            }

            // Gửi email cho khách hàng nếu có email hợp lệ
            if (!empty($this->email_customer) && filter_var($this->email_customer, FILTER_VALIDATE_EMAIL)) {
                Mail::to($this->email_customer)->send(new OrderShipped($order));
            }
            
            Mail::to('tranmanhhieu10@gmail.com')->send(new OrderShipped($order));

            session()->forget('cart_' . Cookie::get('device_id'));
            session()->put('customer_id', $customer->id);
            
            $this->dispatch('clear_cart_after_dat_hang');

            Notification::make()
                ->title('Đặt hàng thành công!')
                ->body('Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn sớm nhất.')
                ->success()
                ->send();

            return redirect()->route('shop.store_front');
        } catch (\Exception $e) {
            Notification::make()
                ->title('Đặt hàng không thành công')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.checkout');
    }
}