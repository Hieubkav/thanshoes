<div>
    <nav data-navbar class="bg-white/95 backdrop-blur-md border-b border-neutral-200 dark:border-neutral-700 dark:bg-neutral-900/95 fixed top-0 right-0 left-0 shadow-soft z-50">
        <!-- Topbar -->
        @include('component.topbar')

        <!-- Top Section: Logo + Search + Icons -->
        <div class="border-b border-neutral-100">
            <div class="flex items-center justify-between mx-auto max-w-screen-xl px-6 py-3 lg:py-4 gap-4">
                <!-- Left: Mobile Menu + Logo -->
                <div class="flex items-center space-x-4 flex-shrink-0">
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button" type="button"
                            class="lg:hidden p-2 rounded-xl hover:bg-primary-50 text-neutral-600 hover:text-primary-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                    </button>

                    <!-- Logo -->
                    @include('components.navbar.logo')
                </div>

                <!-- Center + Right: Search Bar + Icons -->
                <div class="flex items-center flex-1 justify-end">
                    <x-navbar.icons :cartCount="$cartCount" />
                </div>
            </div>
        </div>

        <!-- Bottom Section: Navigation Menu -->
        <div class="bg-white/98">
            <div class="mx-auto max-w-screen-xl px-6">
                @include('components.navbar.navigation')
            </div>
        </div>
    </nav>

    <!-- Include drawer and modals -->
    @include('components.navbar.cart-drawer')
    @include('components.navbar.user-info-modal')
    @include('components.navbar.order-history-modal')
    @include('components.navbar.search-modal')

    <!-- Mobile Menu - Moved outside navbar to avoid z-index stacking context issues -->
    @include('components.navbar.mobile-menu', ['brands' => $brands, 'types' => $types])
</div>
