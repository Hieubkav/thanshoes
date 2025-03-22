<div>
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Form thanh toán -->
            <div class="md:w-2/3">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-2xl font-bold mb-6">Thông tin thanh toán</h2>
                    
                    <!-- Thông tin khách hàng -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="name" class="block text-gray-700 mb-2">Họ và tên:</label>
                            <input type="text" id="name" wire:model="name_customer"
                                class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="phone" class="block text-gray-700 mb-2">Số điện thoại:</label>
                            <input type="text" id="phone" wire:model="phone_customer"
                                class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="email" class="block text-gray-700 mb-2">
                                Email
                                <span class="text-sm text-gray-500">(Có thể để trống)</span>
                            </label>
                            <input type="email" id="email" wire:model="email_customer"
                                class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="address" class="block text-gray-700 mb-2">Địa chỉ:</label>
                            <input type="text" id="address" wire:model="address_customer"
                                class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Hình thức thanh toán -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">Hình thức thanh toán</h3>
                        <div class="space-y-4">
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="cod" wire:model="payment_method"
                                    class="mr-3">
                                <div>
                                    <span class="font-medium">Thanh toán khi nhận hàng (COD)</span>
                                    <p class="text-sm text-gray-500">Thanh toán bằng tiền mặt khi nhận được hàng</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="bank" wire:model="payment_method"
                                    class="mr-3">
                                <div>
                                    <span class="font-medium">Chuyển khoản ngân hàng</span>
                                    <p class="text-sm text-gray-500">Chuyển khoản trực tiếp đến tài khoản của chúng tôi</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- QR Code cho thanh toán -->
                    @if($payment_method === 'bank')
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Mã QR thanh toán</h3>
                            <div class="flex justify-center">
                                <img class="max-w-xs"
                                    src="https://img.vietqr.io/image/970437-{{ $accountNumber }}-compact2.jpg?amount={{ $total }}&addInfo=Đơn giày ThanShoes Chất Lượng!&accountName={{ $accountHolder }}"
                                    alt="QR Code thanh toán">
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tóm tắt đơn hàng -->
            <div class="md:w-1/3">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-bold mb-4">Tóm tắt đơn hàng</h2>
                    
                    <!-- Danh sách sản phẩm -->
                    <div class="space-y-4 mb-6">
                        @foreach($cart as $item)
                            <div class="flex items-center border-b pb-4">
                                <div class="relative">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['product_name'] }}"
                                        class="w-16 h-16 object-cover rounded-md">
                                    <span class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                        {{ $item['quantity'] }}
                                    </span>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="font-medium">{{ $item['product_name'] }}</h3>
                                    <p class="text-sm text-gray-500">{{ $item['variant_color'] }}/{{ $item['variant_size'] }}</p>
                                    <p class="text-sm font-medium">{{ number_format($item['price'], 0, ',', '.') }}đ x {{ $item['quantity'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Tổng tiền -->
                    <div class="space-y-2 border-t pt-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tạm tính:</span>
                            <span class="font-medium">{{ number_format($total, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phí vận chuyển:</span>
                            <span class="text-green-600 font-medium">Miễn phí</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t pt-2">
                            <span>Tổng cộng:</span>
                            <span>{{ number_format($total, 0, ',', '.') }}đ</span>
                        </div>
                    </div>

                    <!-- Nút đặt hàng -->
                    <button wire:click="dat_hang"
                        class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                        wire:loading.class="opacity-75 cursor-not-allowed">
                        <span wire:loading.remove wire:target="dat_hang">Đặt hàng ngay</span>
                        <div wire:loading wire:target="dat_hang" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Đang xử lý...
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
