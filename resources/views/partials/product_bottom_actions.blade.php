<!-- Product Bottom Actions for Mobile -->
<div class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 dark:bg-gray-800 dark:border-gray-700 shadow-lg" 
     x-data="{ 
         isVisible: true,
         lastScrollY: window.scrollY
     }"
     x-init="initScrollBehavior()"
     @scroll.window="handleScroll($event)"
     :class="{ 'translate-y-full': !isVisible }"
     style="transition: transform 0.3s ease-in-out;">

    <!-- Safety bottom padding for iPhone notch -->
    <div class="safe-area-inset-bottom bg-white dark:bg-gray-800">
        <!-- Price Display -->
        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Giá:</span>
                    <div class="flex items-baseline gap-2">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                            @if ($product->variants->min('price') == $product->variants->max('price'))
                                @php
                                    $price = $product->variants->min('price');
                                    $discountedPrice = \App\Helpers\PriceHelper::calculateDiscountedPrice($price);
                                @endphp
                                {{ number_format($discountedPrice, 0, ',', '.') }}₫
                            @else
                                @php
                                    $minPrice = $product->variants->min('price');
                                    $discountedMinPrice = \App\Helpers\PriceHelper::calculateDiscountedPrice($minPrice);
                                @endphp
                                {{ number_format($discountedMinPrice, 0, ',', '.') }}₫
                                <span class="text-xs text-gray-500">-</span>
                            @endif
                        </span>
                        @if($globalDiscountPercent > 0)
                        <span class="text-xs text-gray-400 line-through">
                            @php
                                $displayOriginalPrice = \App\Helpers\PriceHelper::getDisplayOriginalPrice($product->variants->max('price'));
                            @endphp
                            {{ number_format($displayOriginalPrice, 0, ',', '.') }}₫
                        </span>
                        @endif
                    </div>
                </div>
                
                <!-- Stock Status -->
                <div class="text-right">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Tồn kho:</span>
                    <div class="text-sm font-medium text-red-500">
                        @if ($countfilter == 2)
                            {{ $product->variants->where('color', $selectedColor)->where('size', $selectedSize)->sum('stock') }}
                        @else
                            {{ $product->variants->where('size', $selectedSize)->sum('stock') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2 p-3">
            <!-- Quick Buy Button -->
            <button type="button"
                    wire:click="openQuickBuy"
                    wire:loading.attr="disabled"
                    wire:target="openQuickBuy"
                    class="flex-1 px-4 py-3 text-white font-semibold rounded-lg shadow-md bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-400 transition-all duration-300 text-center disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span wire:loading.remove wire:target="openQuickBuy">Mua ngay</span>
                <span wire:loading wire:target="openQuickBuy" class="inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0a10 10 0 100 20v-2a8 8 0 01-8-8z"></path>
                    </svg>
                    <span>Đang mở</span>
                </span>
            </button>

            <!-- Add to Cart Button -->
            <button wire:click="addToCart"
                    wire:loading.attr="disabled"
                    wire:target="addToCart"
                    class="flex-1 px-4 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-lg font-medium hover:bg-gray-800 dark:hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 576 512" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/>
                </svg>
                <span wire:loading.remove wire:target="addToCart">Thêm giỏ</span>
                <span wire:loading wire:target="addToCart" class="flex items-center space-x-2">
                    <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Đang thêm...</span>
                </span>
            </button>
        </div>
    </div>
</div>

<!-- Add safe area padding for iOS devices -->
<style>
    .safe-area-inset-bottom {
        padding-bottom: env(safe-area-inset-bottom, 0px);
    }
</style>

<script>
document.addEventListener('alpine:initializing', () => {
    Alpine.data('productBottomActions', () => ({
        init() {
            this.$el._x_dataStack = this.$el._x_dataStack || [];
            this.initScrollBehavior();
        },
        
        initScrollBehavior() {
            // Handle scroll direction for show/hide
            let lastScrollY = window.scrollY;
            let ticking = false;
            
            const updateVisibility = () => {
                const currentScrollY = window.scrollY;
                const scrollThreshold = 100;
                const isScrollingDown = currentScrollY > lastScrollY + scrollThreshold;
                const isScrollingUp = currentScrollY < lastScrollY - scrollThreshold;
                
                // Show when scrolling up or near bottom of page
                if (isScrollingUp || (window.innerHeight + currentScrollY) >= (document.body.offsetHeight - 200)) {
                    this.isVisible = true;
                } else if (isScrollingDown && currentScrollY > 300) {
                    this.isVisible = false;
                }
                
                lastScrollY = currentScrollY;
                ticking = false;
            };
            
            this.$el.addEventListener('scroll', () => {
                if (!ticking) {
                    window.requestAnimationFrame(updateVisibility);
                    ticking = true;
                }
            });
        },
        
        handleScroll(event) {
            // This will be called by the @scroll.window directive
        }
    }))
});
</script>
