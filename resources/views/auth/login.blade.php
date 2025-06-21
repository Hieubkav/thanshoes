@extends('layouts.shoplayout')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-neutral-50 to-neutral-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-primary-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-user text-primary-600 text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-neutral-900 mb-2">
                Đăng nhập
            </h2>
            <p class="text-neutral-600">
                Chào mừng bạn quay trở lại với ThanShoes
            </p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-2xl shadow-soft-lg p-8 border border-neutral-200/50">
            @livewire('auth.login')
        </div>

        <!-- Register Link -->
        <div class="text-center">
            <p class="text-neutral-600">
                Chưa có tài khoản? 
                <a href="{{ route('register') }}" class="font-semibold text-primary-600 hover:text-primary-700 transition-colors duration-200">
                    Đăng ký ngay
                </a>
            </p>
        </div>

        <!-- Back to Home -->
        <div class="text-center">
            <a href="{{ route('shop.store_front') }}" class="inline-flex items-center space-x-2 text-neutral-500 hover:text-neutral-700 transition-colors duration-200">
                <i class="fas fa-arrow-left"></i>
                <span>Quay lại trang chủ</span>
            </a>
        </div>
    </div>
</div>
@endsection
