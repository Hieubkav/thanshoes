<?php

namespace App\Livewire\Auth;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email',
        'phone' => 'nullable|string',
        'password' => 'required|min:8|confirmed',
    ];

    protected $messages = [
        'name.required' => 'Vui lòng nhập họ và tên',
        'name.max' => 'Họ và tên không được quá 255 ký tự',
        'email.email' => 'Email không hợp lệ',
        'email.unique' => 'Email này đã được sử dụng',
        'phone.unique' => 'Số điện thoại này đã được sử dụng',
        'password.required' => 'Vui lòng nhập mật khẩu',
        'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
        'password.confirmed' => 'Xác nhận mật khẩu không khớp',
    ];

    public function register()
    {
        // Validation tùy chỉnh để đảm bảo có ít nhất email hoặc phone
        if (empty($this->email) && empty($this->phone)) {
            $this->addError('email', 'Vui lòng nhập ít nhất email hoặc số điện thoại');
            $this->addError('phone', 'Vui lòng nhập ít nhất email hoặc số điện thoại');
            return;
        }

        $this->validate();

        // Kiểm tra unique manual
        if (!empty($this->email)) {
            $existingCustomer = Customer::where('email', $this->email)->first();
            if ($existingCustomer) {
                $this->addError('email', 'Email này đã được sử dụng');
                return;
            }
        }

        if (!empty($this->phone)) {
            $existingCustomer = Customer::where('phone', $this->phone)->first();
            if ($existingCustomer) {
                $this->addError('phone', 'Số điện thoại này đã được sử dụng');
                return;
            }
        }

        $customer = Customer::create([
            'name' => $this->name,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'password' => Hash::make($this->password),
        ]);

        $previousSessionId = session()->getId();

        Auth::guard('customers')->login($customer);

        session()->regenerate();

        // Merge cart từ session sang user cart
        $this->mergeSessionCartToUserCart($previousSessionId);

        // Dispatch event để cập nhật navbar
        $this->dispatch('user_logged_in');

        return redirect()->route('shop.store_front')->with('success', 'Đăng ký thành công!');
    }

    private function mergeSessionCartToUserCart(?string $previousSessionId = null)
    {
        $sessionId = $previousSessionId ?? session()->getId();
        $customerId = Auth::guard('customers')->id();

        // Tìm cart của session (guest)
        $sessionCart = \App\Models\Cart::where('session_id', $sessionId)
            ->whereNull('customer_id')
            ->first();

        if (!$sessionCart) {
            return; // Không có cart session
        }

        // Tạo cart cho khách hàng mới
        $userCart = \App\Models\Cart::create([
            'customer_id' => $customerId,
            'session_id' => null,
            'total_amount' => $sessionCart->total_amount ?? 0,
            'original_total_amount' => $sessionCart->original_total_amount ?? ($sessionCart->total_amount ?? 0)
        ]);

        // Chuyển các items từ session cart sang user cart
        foreach ($sessionCart->items as $item) {
            $item->update([
                'cart_id' => $userCart->id
            ]);
        }

        // Xóa session cart
        $sessionCart->delete();
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
