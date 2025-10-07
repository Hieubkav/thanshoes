<div class="space-y-6">
    <div class="flex flex-col gap-4 rounded-2xl border border-neutral-200/60 bg-white p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-neutral-900">Đơn hàng #{{ $order->id }}</h1>
                <p class="text-sm text-neutral-500">Đặt lúc {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            @php
                $statusMap = [
                    'pending' => ['text' => 'Chờ xác nhận', 'color' => 'bg-amber-100 text-amber-700'],
                    'processing' => ['text' => 'Đang xử lý', 'color' => 'bg-blue-100 text-blue-700'],
                    'completed' => ['text' => 'Hoàn thành', 'color' => 'bg-emerald-100 text-emerald-700'],
                    'declined' => ['text' => 'Đã hủy', 'color' => 'bg-rose-100 text-rose-700'],
                ];
                $status = $statusMap[$order->status] ?? ['text' => ucfirst($order->status), 'color' => 'bg-gray-100 text-gray-700'];
            @endphp
            <span class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-full {{ $status['color'] }}">
                {{ $status['text'] }}
            </span>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl border border-neutral-200/60 bg-neutral-50/60 p-4">
                <p class="text-xs uppercase tracking-wide text-neutral-400">Tổng thanh toán</p>
                <p class="mt-1 text-xl font-semibold text-primary-600">
                    {{ number_format($order->total ?? 0, 0, ',', '.') }}đ
                </p>
                @if ($order->original_total && $order->original_total > $order->total)
                    <p class="text-xs text-neutral-400 line-through">
                        {{ number_format($order->original_total, 0, ',', '.') }}đ
                    </p>
                    <p class="text-xs text-emerald-600 mt-1">Tiết kiệm {{ number_format(($order->original_total - $order->total), 0, ',', '.') }}đ</p>
                @endif
            </div>
            <div class="rounded-xl border border-neutral-200/60 bg-neutral-50/60 p-4">
                <p class="text-xs uppercase tracking-wide text-neutral-400">Phương thức thanh toán</p>
                <p class="mt-1 text-sm font-medium text-neutral-700">
                    {{ $order->payment_method === 'bank' ? 'Chuyển khoản ngân hàng' : 'Thanh toán khi nhận hàng (COD)' }}
                </p>
            </div>
            <div class="rounded-xl border border-neutral-200/60 bg-neutral-50/60 p-4">
                <p class="text-xs uppercase tracking-wide text-neutral-400">Số lượng sản phẩm</p>
                <p class="mt-1 text-sm font-medium text-neutral-700">
                    {{ $order->items->sum('quantity') }} món
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-neutral-200/60 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-neutral-900 mb-4">Thông tin nhận hàng</h2>
        <dl class="grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs uppercase tracking-wide text-neutral-400">Họ tên</dt>
                <dd class="text-sm font-medium text-neutral-700">{{ $order->customer->name }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-neutral-400">Số điện thoại</dt>
                <dd class="text-sm font-medium text-neutral-700">{{ $order->customer->phone ?: 'Chưa cập nhật' }}</dd>
            </div>
            <div>
                <dt class="text-xs uppercase tracking-wide text-neutral-400">Email</dt>
                <dd class="text-sm font-medium text-neutral-700">{{ $order->customer->email ?: 'Chưa cập nhật' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs uppercase tracking-wide text-neutral-400">Địa chỉ</dt>
                <dd class="text-sm font-medium text-neutral-700">{{ $order->notes ?: 'Chưa cập nhật' }}</dd>
            </div>
        </dl>
    </div>

    <div class="rounded-2xl border border-neutral-200/60 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-neutral-900 mb-4">Chi tiết sản phẩm</h2>
        <div class="space-y-4">
            @foreach ($order->items as $item)
                @php
                    $image = optional($item->variant->variantImage)->image;
                    if ($image && !filter_var($image, FILTER_VALIDATE_URL)) {
                        $image = asset('storage/' . ltrim($image, '/'));
                    }
                @endphp
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 rounded-xl border border-neutral-200/60 p-4">
                    <div class="w-20 h-20 rounded-xl overflow-hidden bg-neutral-100 flex-shrink-0">
                        @if ($image)
                            <img src="{{ $image }}" alt="{{ $item->variant->product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xs text-neutral-400">
                                Không có ảnh
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-neutral-900">
                            {{ $item->variant->product->name }}
                        </p>
                        <p class="text-xs text-neutral-500 mt-1">
                            Phân loại:
                            @if ($item->variant->color)
                                <span class="font-medium">{{ $item->variant->color }}</span>
                            @endif
                            @if ($item->variant->size)
                                <span class="font-medium">- Size {{ $item->variant->size }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-neutral-500">Số lượng: x{{ $item->quantity }}</p>
                        <p class="text-sm font-semibold text-neutral-900">
                            {{ number_format($item->price, 0, ',', '.') }}đ
                        </p>
                        <p class="text-xs text-neutral-500 mt-1">
                            Tạm tính: {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('customer.orders.index') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-neutral-600 hover:text-neutral-900 transition">
            <i class="fas fa-arrow-left text-xs"></i>
            Quay lại danh sách đơn
        </a>
        <div class="text-sm text-neutral-500">
            Cần hỗ trợ? Liên hệ hotline <span class="font-semibold text-neutral-800">1900.0000</span> hoặc chat với CSKH.
        </div>
    </div>
</div>
