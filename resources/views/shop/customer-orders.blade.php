@extends('layouts.shoplayout')

@section('content')
<div class="bg-neutral-50 py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-neutral-900">Đơn hàng của tôi</h1>
            <p class="mt-2 text-sm text-neutral-500">
                Xem lại lịch sử mua sắm và theo dõi trạng thái đơn hàng của bạn.
            </p>
        </div>

        <livewire:customer-orders />
    </div>
</div>
@endsection
