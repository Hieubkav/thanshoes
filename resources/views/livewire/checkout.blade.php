<div x-data="{
    showQR: @entangle('payment_method').defer === 'bank',
    isSubmitting: false,
    init() {
        this.$watch('showQR', value => {
            if (value) {
                this.$el.querySelector('.qr-section').classList.remove('hidden');
            } else {
                this.$el.querySelector('.qr-section').classList.add('hidden');
            }
        });
    }
}">
    <div class="container mx-auto px-6 py-8 mt-32 md:mt-40">
        <div class="flex flex-col lg:flex-row gap-8 max-w-7xl mx-auto">
            <!-- Form thanh toán -->
            <div class="lg:w-2/3">
                <div class="card card-body">
                    <div class="flex items-center mb-6">
                        <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-credit-card text-white text-sm"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-neutral-900">Thông tin thanh toán</h2>
                    </div>

                    <!-- Thông tin khách hàng -->
                    <form class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user text-primary-500 mr-2"></i>
                                    Họ và tên <span class="text-error-500">*</span>
                                </label>
                                <input type="text" id="name" wire:model.defer="name_customer"
                                    class="form-input @error('name_customer') border-error-500 focus:ring-error-500 @enderror"
                                    placeholder="Nhập họ và tên của bạn"
                                    required>
                                @error('name_customer')
                                    <p class="text-error-500 text-sm mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone text-primary-500 mr-2"></i>
                                    Số điện thoại <span class="text-error-500">*</span>
                                </label>
                                <input type="tel" id="phone" wire:model.defer="phone_customer"
                                    class="form-input @error('phone_customer') border-error-500 focus:ring-error-500 @enderror"
                                    placeholder="Nhập số điện thoại"
                                    required>
                                @error('phone_customer')
                                    <p class="text-error-500 text-sm mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope text-primary-500 mr-2"></i>
                                    Email
                                    <span class="text-sm text-neutral-500 font-normal">(để nhận thông báo)</span>
                                </label>
                                <input type="email" id="email" wire:model.defer="email_customer"
                                    class="form-input @error('email_customer') border-error-500 focus:ring-error-500 @enderror"
                                    placeholder="Nhập địa chỉ email">
                                @error('email_customer')
                                    <p class="text-error-500 text-sm mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt text-primary-500 mr-2"></i>
                                    Địa chỉ <span class="text-error-500">*</span>
                                </label>
                                <input type="text" id="address" wire:model.defer="address_customer"
                                    class="form-input @error('address_customer') border-error-500 focus:ring-error-500 @enderror"
                                    placeholder="Nhập địa chỉ giao hàng"
                                    required>
                                @error('address_customer')
                                    <p class="text-error-500 text-sm mt-1 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Hình thức thanh toán -->
                        <div class="space-y-4">
                            <div class="flex items-center mb-4">
                                <div class="w-6 h-6 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-wallet text-primary-600 text-xs"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-neutral-900">Hình thức thanh toán</h3>
                            </div>

                            <div class="space-y-3">
                                <label class="relative flex items-start p-5 border-2 rounded-xl cursor-pointer transition-all duration-200 hover:bg-neutral-50"
                                    :class="{ 'border-primary-500 bg-primary-50/50 ring-2 ring-primary-500/20': !showQR, 'border-neutral-200': showQR }">
                                    <input type="radio" name="payment_method" value="cod"
                                        wire:model="payment_method" x-on:change="showQR = false"
                                        class="mt-1 text-primary-500 focus:ring-primary-500">
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center">
                                            <i class="fas fa-truck text-primary-500 mr-2"></i>
                                            <span class="font-semibold text-neutral-900">Thanh toán khi nhận hàng (COD)</span>
                                        </div>
                                        <p class="text-sm text-neutral-600 mt-1">Thanh toán bằng tiền mặt khi nhận được hàng</p>
                                        <div class="flex items-center mt-2">
                                            <span class="chip chip-success text-xs">Phổ biến</span>
                                        </div>
                                    </div>
                                </label>

                                <label class="relative flex items-start p-5 border-2 rounded-xl cursor-pointer transition-all duration-200 hover:bg-neutral-50"
                                    :class="{ 'border-primary-500 bg-primary-50/50 ring-2 ring-primary-500/20': showQR, 'border-neutral-200': !showQR }">
                                    <input type="radio" name="payment_method" value="bank"
                                        wire:model="payment_method" x-on:change="showQR = true"
                                        class="mt-1 text-primary-500 focus:ring-primary-500">
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center">
                                            <i class="fas fa-university text-primary-500 mr-2"></i>
                                            <span class="font-semibold text-neutral-900">Chuyển khoản ngân hàng</span>
                                        </div>
                                        <p class="text-sm text-neutral-600 mt-1">Chuyển khoản trực tiếp đến tài khoản của chúng tôi</p>
                                        <div class="flex items-center mt-2">
                                            <span class="chip chip-warning text-xs">Nhanh chóng</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- QR Code cho thanh toán -->
                        <div class="qr-section transition-all duration-300 ease-in-out" x-show="showQR" x-cloak>
                            <h3 class="text-lg font-semibold mb-4">Mã QR thanh toán</h3>
                            <div class="text-sm text-gray-500 mb-4">
                                <p class="font-medium">Thông tin chuyển khoản:</p>
                                <p>Ngân hàng: {{ $bankCode }}</p>
                                <p>Số tài khoản: {{ $accountNumber }}</p>
                                <p>Chủ tài khoản: {{ $accountHolder }}</p>
                            </div>
                            <div class="flex justify-center p-4 bg-gray-50 rounded-lg shadow-inner">
                                <img class="max-w-xs"
                                    src="https://img.vietqr.io/image/{{ $bankCode }}-{{ $accountNumber }}-compact2.jpg?amount={{ $total }}&addInfo=Thanh%20toan%20don%20hang%20ThanShoes&accountName={{ urlencode($accountHolder) }}"
                                    alt="QR Code thanh toán">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="lg:w-1/3">
                <div class="card sticky top-8">
                    <div class="card-body">
                        <div class="flex items-center mb-6">
                            <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-shopping-bag text-white text-sm"></i>
                            </div>
                            <h2 class="text-xl font-bold text-neutral-900">Tóm tắt đơn hàng</h2>
                        </div>

                        <!-- Danh sách sản phẩm -->
                        <div class="space-y-4 mb-6">
                            @foreach ($cartItems as $item)
                                <div class="flex items-center p-4 bg-neutral-50 rounded-lg border border-neutral-200/50">
                                    <div class="relative">
                                        <img src="{{ $item->variant->variantImage->image_url ?? '' }}"
                                             alt="{{ $item->product->name }}"
                                             class="w-16 h-16 object-cover rounded-lg shadow-sm">
                                        <span class="absolute -top-2 -right-2 bg-primary-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-semibold shadow-md">
                                            {{ $item->quantity }}
                                        </span>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <h3 class="font-semibold text-neutral-900 mb-1">{{ $item->product->name }}</h3>
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="chip chip-neutral text-xs">{{ $item->variant->color }}</span>
                                            <span class="chip chip-neutral text-xs">{{ $item->variant->size }}</span>
                                        </div>
                                        <p class="text-sm text-neutral-600">
                                            {{ number_format($item->price, 0, ',', '.') }}đ × {{ $item->quantity }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-lg text-primary-600">
                                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    <!-- Tổng tiền và thông tin giảm giá -->
                    @include('components.checkout.discount-info')

                        <!-- Shipping Info -->
                        <div class="bg-success-50 border border-success-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-shipping-fast text-success-600 mr-2"></i>
                                    <span class="text-neutral-700 font-medium">Phí vận chuyển:</span>
                                </div>
                                <span class="text-success-600 font-bold">Miễn phí</span>
                            </div>
                            <p class="text-xs text-success-600 mt-1 ml-6">Áp dụng cho tất cả đơn hàng</p>
                        </div>

                        <!-- Nút đặt hàng -->
                        <button wire:click="dat_hang" wire:loading.attr="disabled"
                            class="btn btn-primary btn-lg w-full shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove class="flex items-center justify-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                Đặt hàng ngay
                            </span>
                            <span wire:loading class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Đang xử lý...
                            </span>
                        </button>

                        <!-- Security Notice -->
                        <div class="mt-4 text-center">
                            <p class="text-xs text-neutral-500 flex items-center justify-center">
                                <i class="fas fa-shield-alt text-success-500 mr-1"></i>
                                Thông tin của bạn được bảo mật an toàn
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush
