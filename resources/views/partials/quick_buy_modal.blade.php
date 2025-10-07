@php
    use App\Helpers\PriceHelper;
    use App\Helpers\VnLocation;

    $quickBuyProvinces = is_array($quickBuyProvinces ?? null) ? $quickBuyProvinces : [];
    $quickBuyProvinceSelected = isset($quickBuyProvinceSelected)
        ? (bool) $quickBuyProvinceSelected
        : !empty($quickBuyProvince ?? null);
    $quickBuyWards = is_array($quickBuyWards ?? null) ? $quickBuyWards : [];
    if ($quickBuyProvinceSelected && empty($quickBuyWards) && !empty($quickBuyProvince ?? null)) {
        $quickBuyWards = VnLocation::wardsOfProvince((string) $quickBuyProvince);
    }

    $quickBuyStock = 0;
    $hasColorOptions = isset($list_colors) && is_countable($list_colors) && count($list_colors) > 0;
    $quickBuyVariant = null;

    if ($hasColorOptions && !empty($selectedColor) && !empty($selectedSize)) {
        $quickBuyStock = $product->variants
            ->where('color', $selectedColor)
            ->where('size', $selectedSize)
            ->sum('stock');
        $quickBuyVariant = $product->variants->first(function ($variant) use ($selectedColor, $selectedSize) {
            return $variant->color === $selectedColor && $variant->size === $selectedSize;
        });
    } elseif (!$hasColorOptions && !empty($selectedSize)) {
        $quickBuyStock = $product->variants
            ->where('size', $selectedSize)
            ->sum('stock');
        $quickBuyVariant = $product->variants->firstWhere('size', $selectedSize);
    }

    $quickBuyBasePrice = $quickBuyVariant?->price;
    $quickBuyDiscounted = $quickBuyBasePrice !== null
        ? PriceHelper::calculateDiscountedPrice($quickBuyBasePrice)
        : null;

    $quickBuyImage = null;

    if ($quickBuyVariant && $quickBuyVariant->variantImage) {
        $quickBuyImage = $quickBuyVariant->variantImage->image;
    }

    if (!$quickBuyImage && isset($main_image)) {
        $quickBuyImage = $main_image;
    }

    if (!$quickBuyImage) {
        $fallbackVariant = $product->variants->first(function ($variant) {
            return $variant->variantImage;
        });
        $quickBuyImage = $fallbackVariant?->variantImage?->image;
    }

    if ($quickBuyImage && !filter_var($quickBuyImage, FILTER_VALIDATE_URL)) {
        $quickBuyImage = asset('storage/' . ltrim($quickBuyImage, '/'));
    }

    $quickBuyQuantityForPrice = max(1, (int) $quickBuyQuantity);
    $lineTotalDiscounted = null;
    $lineTotalOriginal = null;

    if ($quickBuyVariant) {
        $lineTotalDiscounted = ($quickBuyDiscounted ?? $quickBuyBasePrice) * $quickBuyQuantityForPrice;
        $lineTotalOriginal = $quickBuyBasePrice * $quickBuyQuantityForPrice;
    }

    $summaryBadges = [];
    if ($hasColorOptions) {
        $summaryBadges[] = $selectedColor ?: 'Chưa chọn màu';
    }
    if (($list_sizes instanceof \Illuminate\Support\Collection ? $list_sizes->count() : (is_countable($list_sizes) ? count($list_sizes) : 0)) > 0) {
        $summaryBadges[] = $selectedSize ?: 'Chưa chọn size';
    }
    $summaryBadges[] = 'Số lượng: ' . $quickBuyQuantityForPrice;
@endphp

@if ($showQuickBuyModal)
    <div class="fixed inset-0 z-[60] flex flex-col justify-end bg-black/60 backdrop-blur-sm overflow-y-auto overscroll-contain"
         wire:click.self="closeQuickBuy">
        <div class="mt-auto w-full px-0 pb-4 md:px-6 md:pb-6">
            <div class="relative w-full bg-white dark:bg-gray-900 shadow-2xl rounded-t-3xl border border-gray-100 dark:border-gray-800 md:max-w-5xl md:mx-auto overflow-hidden max-h-[94vh] flex flex-col">
                <div class="absolute inset-x-0 top-3 flex justify-center pointer-events-none">
                    <span class="h-1.5 w-16 rounded-full bg-gray-200 dark:bg-gray-700"></span>
                </div>
                <button type="button"
                        class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        wire:click="closeQuickBuy">
                    <span class="sr-only">Dong</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="flex-1 overflow-y-auto pt-12 pb-4 px-4 md:px-8 space-y-6">
                    @if (!$quickBuySuccess && !empty($quickBuyInlineErrors))
                        <div class="rounded-xl border border-red-200 bg-red-50 text-red-700 p-4 space-y-2">
                            <div class="flex items-center gap-2 font-semibold">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m0 3.75h.008v.008H12zm9-3.75a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Vui lòng kiểm tra lại thông tin</span>
                            </div>
                            <ul class="list-disc list-inside space-y-1 text-sm">
                                @foreach ($quickBuyInlineErrors as $inlineError)
                                    <li>{{ $inlineError }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if ($quickBuySuccess)
                        <div class="flex flex-col items-center justify-center gap-6 py-12 text-center">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.25 7.25a1 1 0 01-1.414 0l-3.25-3.25a1 1 0 011.414-1.42L8.75 11.17l6.543-6.54a1 1 0 011.411-.002z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">ThanShoes đã nhận đơn!</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $quickBuySuccessMessage }}</p>
                            </div>
                            <div class="w-full max-w-md rounded-xl border border-gray-200 bg-white p-4 text-left shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                <dl class="space-y-3">
                                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                        <dt>Mã đơn tạm:</dt>
                                        <dd class="font-semibold text-gray-900 dark:text-gray-100">#{{ $quickBuySuccessOrderCode }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                        <dt>Tổng thanh toán:</dt>
                                        <dd class="text-lg font-semibold text-orange-500">
                                            {{ number_format($quickBuySuccessTotal, 0, ',', '.') }}đ
                                        </dd>
                                    </div>
                                </dl>
                                <p class="mt-3 text-xs text-gray-400 dark:text-gray-500">Chúng tôi sẽ liên hệ để xác nhận và hướng dẫn giao hàng.</p>
                            </div>
                        </div>
                    @else
                                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between md:gap-6 border-b border-gray-100 dark:border-gray-800 pb-4">
                                                <div>
                                                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $quickBuySuccess ? 'Đặt hàng thành công' : 'Mua nhanh' }}</h2>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $quickBuySuccess ? 'Chúng tôi đã ghi nhận đơn hàng của bạn.' : 'Chọn phân loại và điền thông tin để lên đơn nhanh.' }}</p>
                                                </div>
                                                <div class="flex flex-wrap gap-2 text-sm">
                                                    @foreach ($summaryBadges as $badge)
                                                        <span class="inline-flex items-center rounded-full border border-gray-200 dark:border-gray-700 px-3 py-1 bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                                            {{ $badge }}
                                                        </span>
                                                    @endforeach
                                                    @if ($quickBuyVariant)
                                                        <span class="inline-flex items-center rounded-full border border-orange-200 bg-orange-50 text-orange-600 px-3 py-1 font-semibold">
                                                            {{ number_format($quickBuyDiscounted ?? $quickBuyBasePrice, 0, ',', '.') }}d / san pham
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center rounded-full border border-red-200 bg-red-50 text-red-600 px-3 py-1">
                                                            Chọn đầy đủ phân loại để xem giá
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="grid md:grid-cols-2 gap-6">
                                                <div class="space-y-5">
                                                    @if ($quickBuyImage)
                                                        <div class="rounded-xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-800 overflow-hidden shadow-sm">
                                                            <img src="{{ $quickBuyImage }}" alt="{{ $product->name }}" class="w-full h-48 object-cover md:h-60">
                                                        </div>
                                                    @endif

                                                    <div class="space-y-4">
                                                        @if ($hasColorOptions)
                                                            <div class="space-y-2">
                                                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Màu sắc</p>
                                                                <div class="flex flex-wrap gap-2">
                                                                    @foreach ($list_colors as $color)
                                                                        @php
                                                                            $colorDisabled = $product->variants->where('color', $color)->sum('stock') == 0;
                                                                            $colorSelected = $selectedColor === $color;
                                                                            $colorId = 'quick-buy-color-' . md5($color);
                                                                        @endphp
                                                                        <label for="{{ $colorId }}" class="cursor-pointer">
                                                                            <input id="{{ $colorId }}" type="radio" class="hidden" value="{{ $color }}"
                                                                                   wire:model.live.debounce.150ms="selectedColor"
                                                                                   @if($colorDisabled) disabled @endif>
                                                                            <span class="inline-flex items-center px-3 py-2 rounded-lg border text-sm font-semibold transition
                                                                                {{ $colorDisabled ? 'opacity-40 cursor-not-allowed border-gray-200' : ($colorSelected ? 'border-orange-500 bg-orange-50 text-orange-600 shadow-sm' : 'border-gray-200 hover:border-orange-300 hover:bg-orange-50/40') }}">
                                                                                {{ $color }}
                                                                            </span>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (($list_sizes instanceof \Illuminate\Support\Collection ? $list_sizes->count() : (is_countable($list_sizes) ? count($list_sizes) : 0)) > 0)
                                                            <div class="space-y-2">
                                                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Size</p>
                                                                <div class="flex flex-wrap gap-2">
                                                                    @foreach ($list_sizes as $size)
                                                                        @php
                                                                            $sizeDisabled = $hasColorOptions
                                                                                ? $product->variants
                                                                                    ->where('size', $size)
                                                                                    ->where('color', $selectedColor)
                                                                                    ->sum('stock') == 0
                                                                                : $product->variants->where('size', $size)->sum('stock') == 0;
                                                                            $sizeSelected = $selectedSize === $size;
                                                                            $sizeId = 'quick-buy-size-' . md5($size);
                                                                        @endphp
                                                                        <label for="{{ $sizeId }}" class="cursor-pointer">
                                                                            <input id="{{ $sizeId }}" type="radio" class="hidden" value="{{ $size }}"
                                                                                   wire:model.live.debounce.150ms="selectedSize"
                                                                                   @if($sizeDisabled) disabled @endif>
                                                                            <span class="inline-flex items-center px-3 py-2 rounded-lg border text-sm font-semibold transition
                                                                                {{ $sizeDisabled ? 'opacity-40 cursor-not-allowed border-gray-200' : ($sizeSelected ? 'border-gray-900 bg-gray-900 text-white shadow-sm' : 'border-gray-200 hover:border-gray-400') }}">
                                                                                {{ $size }}
                                                                            </span>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="space-y-3">
                                                        <div class="space-y-2">
                                                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Số lượng</p>
                                                            <div class="flex items-center gap-3">
                                                                <button type="button"
                                                                        wire:click="decrementQuickBuyQuantity"
                                                                        class="h-9 w-9 inline-flex items-center justify-center rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition disabled:opacity-40"
                                                                        @if($quickBuyQuantityForPrice <= 1) disabled @endif>
                                                                    &minus;
                                                                </button>
                                                                <input type="number"
                                                                       min="1"
                                                                       wire:model.debounce.300ms="quickBuyQuantity"
                                                                       class="w-16 text-center rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                                                       inputmode="numeric">
                                                                <button type="button"
                                                                        wire:click="incrementQuickBuyQuantity"
                                                                        class="h-9 w-9 inline-flex items-center justify-center rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition"
                                                                        @if($quickBuyVariant && $quickBuyStock > 0 && $quickBuyQuantityForPrice >= $quickBuyStock) disabled @endif>
                                                                    +
                                                                </button>
                                                            </div>
                                                            @if ($quickBuyStock > 0)
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">Còn {{ $quickBuyStock }} sản phẩm sẵn có.</p>
                                                            @elseif(!empty($selectedSize) || !empty($selectedColor))
                                                                <p class="text-xs text-red-500">Phân loại này hiện hết hàng.</p>
                                                            @endif
                                                        </div>

                                                        @if ($quickBuyVariant)
                                                            <div class="rounded-lg border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60 p-4 space-y-1">
                                                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Tạm tính</p>
                                                                <div class="flex items-baseline gap-3">
                                                                    <span class="text-xl font-bold text-orange-600">
                                                                        {{ number_format($lineTotalDiscounted, 0, ',', '.') }}d
                                                                    </span>
                                                                    @if ($quickBuyDiscounted !== null && $quickBuyDiscounted < $quickBuyBasePrice)
                                                                        <span class="text-sm text-gray-400 line-through">
                                                                            {{ number_format($lineTotalOriginal, 0, ',', '.') }}d
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="space-y-4">
                                                    <div class="space-y-3">
                                                        <div>
                                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Họ tên *</label>
                                                            <input type="text"
                                                                   wire:model.defer="quickBuyName"
                                                                   autocomplete="name"
                                                                   class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                                                   placeholder="Nguyen Van A">
                                                        </div>
                                                        <div>
                                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Số điện thoại *</label>
                                                            <input type="tel"
                                                                   wire:model.defer="quickBuyPhone"
                                                                   autocomplete="tel"
                                                                   class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                                                   placeholder="0123 456 789">
                                                        </div>
                                                        <div class="space-y-1">
                                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Email <span class="font-normal text-gray-400">(tùy chọn)</span></label>
                                                            <input type="email"
                                                                   wire:model.defer="quickBuyEmail"
                                                                   autocomplete="email"
                                                                   class="w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                                                   placeholder="email@example.com">
                                                        </div>
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                            <div>
                                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tỉnh/Thành *</label>
                                                                <select wire:model.live="quickBuyProvince"
                                                                        class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                                                                    <option value="">Chọn tỉnh thành</option>
                                                                    @foreach ($quickBuyProvinces as $province)
                                                                        <option value="{{ $province['id'] }}">{{ $province['name'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <label class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Phường/Xã *</label>
                                                                <select wire:model.live="quickBuyWard"
                                                                        @disabled(empty($quickBuyProvince))
                                                                        class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 disabled:bg-gray-100 disabled:text-gray-400">
                                                                    <option value="">Chọn phường xã</option>
                                                                    @foreach ($quickBuyWards as $ward)
                                                                        <option value="{{ $ward['id'] }}">{{ $ward['name'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Địa chỉ chi tiết *</label>
                                                            <textarea rows="3"
                                                                      wire:model.defer="quickBuyAddressDetail"
                                                                      autocomplete="street-address"
                                                                      class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                                                      placeholder="So nha, ten duong, toa nha..."></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="space-y-3">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Phương thức thanh toán</p>
                                                        <div class="flex flex-wrap gap-3">
                                                            <button type="button"
                                                                    wire:click="$set('quickBuyPaymentMethod','cod')"
                                                                    wire:loading.attr="disabled"
                                                                    class="inline-flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-semibold transition focus:outline-none {{ $quickBuyPaymentMethod === 'cod' ? 'border-gray-900 bg-gray-900 text-white shadow-sm' : 'border-gray-200 text-gray-600 hover:border-gray-400' }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h18M3 9h18M3 15h18M3 21h18" />
                                                                </svg>
                                                                COD (Thanh toán khi nhận hàng)
                                                            </button>
                                                            <button type="button"
                                                                    wire:click="$set('quickBuyPaymentMethod','bank')"
                                                                    wire:loading.attr="disabled"
                                                                    class="inline-flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-semibold transition focus:outline-none {{ $quickBuyPaymentMethod === 'bank' ? 'border-orange-500 bg-orange-50 text-orange-600 shadow-sm' : 'border-gray-200 text-gray-600 hover:border-gray-400' }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75V21h15V9.75" />
                                                                </svg>
                                                                Chuyển khoản ngân hàng
                                                            </button>
                                                        </div>

                                                        @if ($quickBuyPaymentMethod === 'bank' && $settings)
                                                            <div class="rounded-lg border border-orange-200 bg-orange-50 text-sm text-orange-700 p-4 space-y-1 dark:bg-orange-500/10 dark:border-orange-500/40 dark:text-orange-200">
                                                                <p>Ngân hàng: <span class="font-semibold">{{ $settings->bank_name }}</span></p>
                                                                <p>Số tài khoản: <span class="font-semibold">{{ $settings->bank_number }}</span></p>
                                                                <p>Chủ tài khoản: <span class="font-semibold">{{ $settings->bank_account_name }}</span></p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                    @endif
                </div>
                <div class="flex-none border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 px-4 md:px-8 py-4">
                    @if ($quickBuySuccess)
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div class="text-left space-y-1">
                                <span class="text-xs uppercase tracking-wide text-gray-400">Đơn hàng đã ghi nhận</span>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">#{{ $quickBuySuccessOrderCode }} · {{ number_format($quickBuySuccessTotal, 0, ',', '.') }}đ</p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <a href="{{ route('customer.orders.show', ['order' => $quickBuySuccessOrderCode]) }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 text-sm font-semibold shadow transition">
                                    Xem chi tiết đơn
                                </a>
                                <button type="button"
                                        wire:click="closeQuickBuy"
                                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-600 transition hover:border-gray-400 dark:border-gray-700 dark:text-gray-200 dark:hover:border-gray-500">
                                    Tiếp tục mua sắm
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div class="flex items-center gap-3 text-left">
                                <div class="flex flex-col">
                                    <span class="text-xs uppercase tracking-wide text-gray-400">Tổng thanh toán</span>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                            @if ($lineTotalDiscounted)
                                                {{ number_format($lineTotalDiscounted, 0, ',', '.') }}d
                                            @else
                                                ---
                                            @endif
                                        </span>
                                        @if ($lineTotalOriginal && $quickBuyDiscounted !== null && $quickBuyDiscounted < $quickBuyBasePrice)
                                            <span class="text-sm text-gray-400 line-through">
                                                {{ number_format($lineTotalOriginal, 0, ',', '.') }}d
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <button type="button"
                                    wire:click="submitQuickBuy"
                                    wire:loading.attr="disabled"
                                    wire:target="submitQuickBuy"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 font-semibold shadow transition disabled:opacity-60 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="submitQuickBuy">Xác nhận mua ngay</span>
                                <span wire:loading wire:target="submitQuickBuy" class="inline-flex items-center gap-2">
                                    <svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V2a10 10 0 100 20v-2a8 8 0 01-8-8z"></path>
                                    </svg>
                                    <span>Đang xử lý</span>
                                </span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
