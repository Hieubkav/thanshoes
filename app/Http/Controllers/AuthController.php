<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Hiển thị trang đăng nhập khách hàng
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Hiển thị trang đăng ký khách hàng
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng xuất
     */
    public function logout(Request $request)
    {
        if (Auth::guard('customers')->check()) {
            Auth::guard('customers')->logout();
        }
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('shop.store_front')->with('success', 'Đã đăng xuất thành công');
    }
}
