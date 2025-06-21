<?php

namespace App\Livewire\Auth;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $login_field = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'login_field' => 'required',
        'password' => 'required',
    ];

    protected $messages = [
        'login_field.required' => 'Vui lòng nhập email hoặc số điện thoại',
        'password.required' => 'Vui lòng nhập mật khẩu',
    ];

    public function login()
    {
        $this->validate();

        // Xác định xem login_field là email hay phone
        $loginType = $this->isEmail($this->login_field) ? 'email' : 'phone';

        $credentials = [
            $loginType => $this->login_field,
            'password' => $this->password
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();

            // Merge cart từ session sang user cart
            $this->mergeSessionCartToUserCart();

            // Dispatch event để cập nhật navbar
            $this->dispatch('user_logged_in');

            return redirect()->intended(route('shop.store_front'));
        }

        $this->addError('login_field', 'Thông tin đăng nhập không chính xác');
    }

    private function isEmail($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function mergeSessionCartToUserCart()
    {
        $sessionId = session()->getId();
        $userId = auth()->id();

        // Tìm cart của session (guest)
        $sessionCart = \App\Models\Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->first();

        if (!$sessionCart) {
            return; // Không có cart session
        }

        // Tìm hoặc tạo cart cho user
        $userCart = \App\Models\Cart::firstOrCreate([
            'user_id' => $userId,
            'session_id' => null
        ]);

        // Chuyển các items từ session cart sang user cart
        foreach ($sessionCart->items as $item) {
            // Kiểm tra xem item đã tồn tại trong user cart chưa
            $existingItem = $userCart->items()
                ->where('variant_id', $item->variant_id)
                ->first();

            if ($existingItem) {
                // Cộng dồn số lượng
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $item->quantity
                ]);
            } else {
                // Tạo item mới trong user cart
                $item->update([
                    'cart_id' => $userCart->id
                ]);
            }
        }

        // Xóa session cart
        $sessionCart->delete();

        // Cập nhật tổng tiền cho user cart
        $userCart->updateTotal();
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}