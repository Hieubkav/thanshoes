<!-- Modern Add to Cart Button -->
<button wire:click="addToCart"
        wire:loading.attr="disabled"
        wire:target="addToCart"
        class="btn btn-primary btn-lg group relative overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed"
        x-data="{ hover: false }"
        @mouseenter="hover = true"
        @mouseleave="hover = false">

    <!-- Background Animation -->
    <div class="absolute inset-0 bg-gradient-to-r from-primary-600 to-primary-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></div>

    <!-- Button Content -->
    <div class="relative flex items-center justify-center space-x-2">
        <!-- Cart Icon -->
        <svg class="w-5 h-5 transition-transform duration-300 group-hover:scale-110"
             :class="{ 'animate-bounce': hover }"
             fill="currentColor"
             viewBox="0 0 576 512"
             xmlns="http://www.w3.org/2000/svg">
            <path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/>
        </svg>

        <!-- Button Text -->
        <span wire:loading.remove wire:target="addToCart" class="font-semibold">
            Thêm giỏ hàng
        </span>

        <!-- Loading State -->
        <span wire:loading wire:target="addToCart" class="flex items-center space-x-2">
            <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="font-semibold">Đang thêm...</span>
        </span>
    </div>

    <!-- Subtle highlight overlay for hover feedback -->
    <div class="absolute inset-0 bg-primary-500/20 transform scale-0 rounded-lg transition-transform duration-300"
         :class="{ 'scale-100': hover }"
         style="animation-delay: 0.1s;">
    </div>
</button>

<!-- Styles are now handled by Tailwind CSS classes -->
