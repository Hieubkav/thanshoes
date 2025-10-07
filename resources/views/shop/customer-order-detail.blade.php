@extends('layouts.shoplayout')

@section('content')
<div class="bg-neutral-50 py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <a href="{{ route('customer.orders.index') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-neutral-600 hover:text-neutral-900 transition">
                <i class="fas fa-arrow-left text-xs"></i>
                Quay lại đơn hàng của tôi
            </a>
        </div>

        <livewire:customer-order-detail :order="$order" />
    </div>
</div>
@endsection
