<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Variant;
use App\Models\User;
use App\Mail\OrderShipped;
use App\Services\PriceService;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Filament\Notifications\Notification;

class Checkout extends Component
{
    public $cartItems;
    public $total = 0;
    public $originalTotal = 0;
    public $discountAmount = 0;
    public $discountPercentage = 0;
    public $discountApplied = false;
    public $discountType = '';
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
        $cart = Cart::getCart(auth()->id(), session()->getId());
        
        if (!$cart || $cart->items()->count() === 0) {
            return redirect()->route('shop.store_front')
                ->with('error', 'Giỏ hàng của bạn đang trống');
        }

        $this->cartItems = $cart->items()->with(['product', 'variant.variantImage'])->get();
        
        // Tính toán giảm giá
        $this->originalTotal = $cart->original_total_amount ?? $cart->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        
        // Áp dụng giảm giá nếu được bật trong cài đặt
        $setting = Setting::first();
        if ($setting && $setting->apply_price === 'apply' && $setting->dec_product_price > 0) {
            $discountInfo = PriceService::getDiscountInfo($this->originalTotal);
            $this->total = $discountInfo['discounted_price'];
            $this->discountAmount = $discountInfo['discount_amount'];
            $this->discountPercentage = $discountInfo['discount_percentage'];
            $this->discountApplied = true;
            $this->discountType = $discountInfo['discount_type'];
        } else {
            $this->total = $this->originalTotal;
            $this->discountApplied = false;
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
        
        // Fix: Check if Setting exists before calling first()
        $setting = Setting::first();
        if ($setting) {
            $this->bankCode = $setting->bank_name;
            $this->accountNumber = $setting->bank_number;
            $this->accountHolder = $setting->bank_account_name;
        } else {
            $this->bankCode = '';
            $this->accountNumber = '';
            $this->accountHolder = '';
        }
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
            // Tạo hoặc cập nhật thông tin khách hàng
            $customer = Customer::updateOrCreate(
                ['phone' => $this->phone_customer],
                [
                    'name' => $this->name_customer,
                    'email' => $this->email_customer,
                    'address' => $this->address_customer
                ]
            );

            // Tạo đơn hàng mới với thông tin giảm giá
            $order = Order::create([
                'customer_id' => $customer->id,
                'total' => $this->total,
                'original_total' => $this->discountApplied ? $this->originalTotal : null,
                'discount_amount' => $this->discountApplied ? $this->discountAmount : null,
                'discount_type' => $this->discountApplied ? $this->discountType : null,
                'discount_percentage' => $this->discountApplied ? $this->discountPercentage : null,
                'payment_method' => $this->payment_method,
                'status' => 'pending'
            ]);

            // Xử lý từng sản phẩm trong giỏ hàng
            foreach ($this->cartItems as $item) {
                // Kiểm tra tồn kho
                if (!$item->variant || $item->variant->stock < $item->quantity) {
                    throw new \Exception("Sản phẩm {$item->product->name} không đủ số lượng trong kho");
                }

                // Tạo chi tiết đơn hàng
                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price
                ]);

                // Cập nhật số lượng tồn kho
                $item->variant->decrement('stock', $item->quantity);
            }

            // Gửi email cho tất cả user và admin
            // Load the order with all relationships needed in the email template
            $orderWithRelations = Order::with(['items.variant.variantImage', 'items.variant.product'])->find($order->id);
            
            $users = User::all();
            foreach ($users as $user) {
                Mail::to($user->email)->send(new OrderShipped($orderWithRelations));
            }

            Mail::to('tranmanhhieu10@gmail.com')->send(new OrderShipped($orderWithRelations));

            // Xóa giỏ hàng
            $cart = Cart::getCart(auth()->id(), session()->getId());
            $cart->items()->delete();
            $cart->delete();
            
            // Lưu ID khách hàng vào session
            session()->put('customer_id', $customer->id);
            
            // Thông báo cho các component khác
            $this->dispatch('clear_cart_after_dat_hang');

            // Hiển thị thông báo thành công
            Notification::make()
                ->title('Đặt hàng thành công!')
                ->body('Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn sớm nhất.')
                ->success()
                ->send();

            return redirect()->route('shop.store_front');

        } catch (\Exception $e) {
            // Xử lý lỗi và hiển thị thông báo
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