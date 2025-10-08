<div>
    <nav data-navbar class="bg-white/95 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-700 dark:bg-neutral-900/95 fixed top-0 right-0 left-0 shadow-soft z-50">
        <!-- Topbar -->
        @include('component.topbar')

        @if($pendingOrdersCount > 0)
            <div class="bg-amber-50 border-b border-amber-200 text-amber-800 text-[11px]">
                <div class="max-w-screen-xl mx-auto px-2.5 py-0.5 flex items-center justify-between gap-2.5">
                    <div class="flex items-center gap-1">
                        <i class="fas fa-info-circle text-[10px]"></i>
                        <span>
                            Bạn có {{ $pendingOrdersCount }} đơn hàng đang chờ xử lý.
                        </span>
                    </div>
                    <a href="{{ route('customer.orders.index') }}"
                       class="inline-flex items-center gap-1 font-semibold text-amber-900 hover:text-amber-700 transition text-[10px] sm:text-[11px]">
                        Xem ngay
                        <i class="fas fa-arrow-right text-[8px]"></i>
                    </a>
                </div>
            </div>
        @endif

        <!-- Top Section: Logo + Search + Icons -->
        <div class="border-b border-neutral-100">
            <div class="flex items-center justify-between mx-auto max-w-screen-xl px-2.5 py-2 lg:py-1.5 gap-3.5">
                <!-- Left: Mobile Menu + Logo -->
                <div class="flex items-center space-x-3 flex-1 lg:flex-initial">
                    <!-- Mobile Menu Button - Optimized for touch -->
                    <button id="mobile-menu-button" type="button"
                            class="lg:hidden p-2.5 sm:p-3 rounded-xl hover:bg-primary-50 text-neutral-600 hover:text-primary-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                    </button>

                    <!-- Logo - Mobile Optimized -->
                    @include('components.navbar.logo')
                </div>

                <!-- Mobile-centered Search (hidden on larger screens) -->
                <div class="flex-1 max-w-xs mx-auto hidden md:flex justify-center">
                    <div class="relative group w-full">
                        <button type="button"
                                class="w-full flex items-center space-x-2 px-3 py-2 bg-neutral-50 hover:bg-neutral-100 border border-neutral-200 hover:border-primary-300 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                                data-modal-target="search_modal"
                                data-modal-toggle="search_modal">
                            <i class="fas fa-search text-neutral-500 group-hover:text-primary-600 transition-colors duration-200 text-sm"></i>
                            <span class="text-neutral-500 group-hover:text-neutral-700 transition-colors duration-200 flex-1 text-left text-sm">Tìm kiếm...</span>
                        </button>
                    </div>
                </div>

                <!-- Right: Icons -->
                <div class="flex items-center gap-1 sm:gap-2">
                    <x-navbar.icons :cart-count="$cartCount" :pending-orders-count="$pendingOrdersCount" />
                </div>
            </div>
        </div>

        <!-- Bottom Section: Navigation Menu -->
        <div class="bg-white/98">
            <div class="mx-auto max-w-screen-xl px-4">
                @include('components.navbar.navigation')
            </div>
        </div>
    </nav>

    <!-- Include drawer and modals -->
    @include('components.navbar.cart-drawer')
    @include('components.navbar.user-info-modal', ['pendingOrdersCount' => $pendingOrdersCount])
    @include('components.navbar.order-history-modal', ['pendingOrdersCount' => $pendingOrdersCount])
    @include('components.navbar.search-modal')

    <!-- Mobile Menu - Moved outside navbar to avoid z-index stacking context issues -->
    @include('components.navbar.mobile-menu', ['brands' => $brands, 'types' => $types])
</div>

