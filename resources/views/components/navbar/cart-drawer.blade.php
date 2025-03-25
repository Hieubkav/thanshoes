<!-- Cart Drawer Component -->
<div id="drawer_cart"
     class="fixed top-0 right-0 z-[9999] h-screen pt-16 w-3/4 lg:w-1/5 transition-transform translate-x-full bg-gradient-to-r from-orange-100 via-gray-100 to-blue-100 shadow-lg dark:bg-gray-900"
     tabindex="-1" aria-labelledby="drawer-title" aria-hidden="true">
    
    <div class="p-4 flex flex-col h-full">
        <!-- Header Section -->
        <div class="flex justify-between items-center border-b pb-0">
            <h5 id="drawer-title" class="text-lg font-bold text-gray-900 dark:text-white">
                Giỏ Hàng
            </h5>
            <button type="button" data-drawer-hide="drawer_cart"
                    class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <!-- Clear Cart Button -->
        <div class="flex justify-end py-2" wire:click='clear_cart'>
            <button class="text-sm text-red-600 dark:text-red-400 hover:underline">
                Xoá giỏ
            </button>
        </div>

        <!-- Cart Content -->
        <div class="flex-1 overflow-y-auto mt-0 text-sm">
            @forelse ($cartItems as $item)
                <div class="flex items-center border-b py-1">
                    <!-- Product Image with Quantity Badge -->
                    <div class="relative inline-block">
                        <img src="{{ optional($item->variant->variant_images->first())->image ?? '' }}" 
                             alt="{{ optional($item->product)->name }}"
                             class="w-6 h-6 lg:w-8 lg:h-8 rounded-md object-cover">
                        <span class="absolute text-xs top-0 right-0 inline-flex items-center justify-center px-1 py-0 font-bold leading-none text-red-100 bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2 shadow-lg">
                            {{ $item->quantity }}
                        </span>
                    </div>

                    <!-- Product Details -->
                    <div class="ml-3 flex-1">
                        <h5 class="font-medium text-gray-800 dark:text-white">
                            {{ optional($item->product)->name }}
                        </h5>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Phiên bản: {{ optional($item->variant)->color }}/{{ optional($item->variant)->size }}
                        </p>
                        
                        <!-- Quantity Controls -->
                        <div class="flex items-center space-x-2 mt-1">
                            <button wire:click="updateQuantity('{{ $item->id }}', -1)"
                                    class="text-gray-500 hover:text-gray-700 p-1">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                            <span class="text-gray-700">{{ $item->quantity }}</span>
                            <button wire:click="updateQuantity('{{ $item->id }}', 1)"
                                    class="text-gray-500 hover:text-gray-700 p-1">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Price and Remove Button -->
                    <div class="flex flex-col items-end">
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ number_format($item->price, 0, ',', '.') }}đ <br>
                            {{ number_format($item->getTotalPrice(), 0, ',', '.') }}đ
                        </span>
                        <button wire:click="removeItem('{{ $item->id }}')"
                                class="mt-1 text-red-500 hover:text-red-700">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <!-- Empty Cart State -->
                <div class="flex flex-col items-center justify-center py-10">
                    <i class="fa-solid fa-cart-arrow-down text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Giỏ hàng trống</p>
                </div>
            @endforelse
        </div>

        <!-- Cart Footer -->
        @if($cartCount > 0)
            <div class="mt-auto border-t pt-4">
                <!-- Total -->
                <div class="flex justify-between mb-3">
                    <span class="text-gray-500 dark:text-gray-400">Tổng cộng:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">
                        {{ number_format($totalAmount, 0, ',', '.') }}đ
                    </span>
                </div>
                
                <!-- Checkout Button -->
                <div class="mt-4">
                    <a href="{{ route('shop.checkout') }}"
                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-center transition duration-200">
                        Đến trang thanh toán
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
