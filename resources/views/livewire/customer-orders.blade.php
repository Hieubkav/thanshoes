<div class="space-y-6">
    @if ($orders->count())
        <div class="space-y-4">
            @foreach ($orders as $order)
                @php
                    $statusMap = [
                        'pending' => ['text' => 'Chờ xác nhận', 'color' => 'bg-amber-100 text-amber-700'],
                        'processing' => ['text' => 'Đang xử lý', 'color' => 'bg-blue-100 text-blue-700'],
                        'completed' => ['text' => 'Hoàn thành', 'color' => 'bg-emerald-100 text-emerald-700'],
                        'declined' => ['text' => 'Đã hủy', 'color' => 'bg-rose-100 text-rose-700'],
                    ];
                    $status = $statusMap[$order->status] ?? ['text' => ucfirst($order->status), 'color' => 'bg-gray-100 text-gray-700'];
                @endphp
                <div class="rounded-2xl border border-neutral-200/60 shadow-sm bg-white transition hover:border-primary-200 hover:shadow-md {{ $recentOrderId === $order->id ? 'ring-2 ring-orange-400/80' : '' }}">
                    <div class="flex flex-col gap-4 p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-neutral-900">Đơn hàng #{{ $order->id }}</h3>
                                <p class="text-sm text-neutral-500">Đặt lúc {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $status['color'] }}">
                                {{ $status['text'] }}
                            </span>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-3">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-neutral-400">Sản phẩm</p>
                                <p class="text-sm font-medium text-neutral-700">
                                    {{ $order->items->sum('quantity') }} món
                                </p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-neutral-400">Tổng thanh toán</p>
                                <p class="text-sm font-semibold text-primary-600">
                                    {{ number_format($order->total ?? 0, 0, ',', '.') }}đ
                                </p>
                                @if ($order->original_total && $order->original_total > $order->total)
                                    <p class="text-xs text-neutral-400 line-through">
                                        {{ number_format($order->original_total, 0, ',', '.') }}đ
                                    </p>
                                @endif
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-neutral-400">Thanh toán</p>
                                <p class="text-sm font-medium text-neutral-700">
                                    {{ $order->payment_method === 'bank' ? 'Chuyển khoản ngân hàng' : 'Thanh toán khi nhận hàng (COD)' }}
                                </p>
                            </div>
                        </div>
                        @if ($order->items->count())
                            <div class="border border-neutral-200/60 rounded-xl p-4 bg-neutral-50/70">
                                <ul class="space-y-3">
                                    @foreach ($order->items->take(3) as $item)
                                        <li class="flex items-start gap-3">
                                            @php
                                                $image = optional($item->variant->variantImage)->image;
                                                if ($image && !filter_var($image, FILTER_VALIDATE_URL)) {
                                                    $image = asset('storage/' . ltrim($image, '/'));
                                                }
                                            @endphp
                                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-white border border-neutral-200/60 flex-shrink-0">
                                                @if ($image)
                                                    <img src="{{ $image }}" alt="{{ $item->variant->product->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-xs text-neutral-400">
                                                        N/A
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-neutral-800 truncate">
                                                    {{ $item->variant->product->name }}
                                                </p>
                                                <p class="text-xs text-neutral-500">
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
                                                <p class="text-xs text-neutral-500">x{{ $item->quantity }}</p>
                                                <p class="text-sm font-semibold text-neutral-800">
                                                    {{ number_format($item->price, 0, ',', '.') }}đ
                                                </p>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                @if ($order->items->count() > 3)
                                    <p class="mt-3 text-xs text-neutral-500">
                                        ... và {{ $order->items->count() - 3 }} sản phẩm khác
                                    </p>
                                @endif
                            </div>
                        @endif
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <p class="text-sm text-neutral-500">
                                Giao tới: <span class="font-medium text-neutral-700">{{ $order->notes ?: 'Chưa cập nhật địa chỉ' }}</span>
                            </p>
                            <a href="{{ route('customer.orders.show', $order) }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-primary-500 text-white hover:bg-primary-600 transition">
                                Xem chi tiết
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div>
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-20 bg-white border border-dashed border-neutral-200 rounded-2xl">
            <i class="fas fa-box-open text-4xl text-neutral-300 mb-4"></i>
            <h3 class="text-lg font-semibold text-neutral-700">Bạn chưa có đơn hàng nào</h3>
            <p class="text-sm text-neutral-500 mt-2">Khám phá sản phẩm và đặt hàng ngay hôm nay.</p>
            <a href="{{ route('shop.store_front') }}"
               class="inline-flex items-center justify-center gap-2 mt-4 px-4 py-2 text-sm font-semibold text-white bg-primary-500 rounded-lg hover:bg-primary-600 transition">
                Mua sắm ngay
            </a>
        </div>
    @endif
</div>
