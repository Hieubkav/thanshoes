<!-- Modern Navigation Icons -->
<div class="flex items-center space-x-4">
    <!-- Extended Search Bar -->
    <div class="relative group hidden sm:block">
        <button type="button"
                class="flex items-center space-x-3 px-4 py-2.5 bg-neutral-50 hover:bg-neutral-100 border border-neutral-200 hover:border-primary-300 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500/20 w-64 lg:w-80"
                data-modal-target="search_modal"
                data-modal-toggle="search_modal">
            <i class="fas fa-search text-neutral-500 group-hover:text-primary-600 transition-colors duration-200"></i>
            <span class="text-neutral-500 group-hover:text-neutral-700 transition-colors duration-200 flex-1 text-left">Tìm kiếm sản phẩm...</span>
            <div class="hidden lg:flex items-center space-x-1 text-neutral-400">
                <kbd class="px-2 py-1 text-xs bg-neutral-200 rounded border">Ctrl</kbd>
                <span class="text-xs">+</span>
                <kbd class="px-2 py-1 text-xs bg-neutral-200 rounded border">K</kbd>
            </div>
        </button>
    </div>

    <!-- Mobile Search Icon -->
    <div class="relative group sm:hidden">
        <button type="button"
                class="group relative p-3 rounded-xl hover:bg-primary-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                data-modal-target="search_modal"
                data-modal-toggle="search_modal">
            <i class="fas fa-search text-neutral-600 group-hover:text-primary-600 transition-colors duration-200"></i>
            <div class="absolute inset-0 rounded-xl bg-primary-500/10 scale-0 group-hover:scale-100 transition-transform duration-200"></div>
        </button>
    </div>

    <!-- User Dropdown -->
    <div class="relative">
        <button type="button"
                class="group relative p-3 rounded-xl hover:bg-primary-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                data-dropdown-toggle="dropdown_user">
            <i class="fas fa-user text-neutral-600 group-hover:text-primary-600 transition-colors duration-200 text-lg"></i>
            <div class="absolute inset-0 rounded-xl bg-primary-500/10 scale-0 group-hover:scale-100 transition-transform duration-200"></div>
        </button>

        <!-- Modern Dropdown Menu -->
        <div id="dropdown_user"
             class="z-50 hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-soft-lg border border-neutral-200/50 overflow-hidden">
            <div class="py-2">
                @auth
                    <!-- Authenticated Customer Menu -->
                    <div class="px-4 py-3 border-b border-neutral-100">
                        <p class="text-sm font-semibold text-neutral-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-neutral-500">
                            {{ Auth::user()->email ?: Auth::user()->phone }}
                        </p>
                    </div>

                    <a href="#"
                       data-modal-target="info_user_modal"
                       data-modal-toggle="info_user_modal"
                       class="flex items-center px-4 py-3 text-sm text-neutral-700 hover:bg-primary-50 hover:text-primary-600 transition-all duration-200">
                        <i class="fas fa-user-circle w-5 mr-3 text-primary-500"></i>
                        <div>
                            <div class="font-medium">Thông tin cá nhân</div>
                            <div class="text-xs text-neutral-500">Xem và chỉnh sửa thông tin</div>
                        </div>
                    </a>

                    <a href="#"
                       data-modal-target="info_user_order"
                       data-modal-toggle="info_user_order"
                       class="flex items-center px-4 py-3 text-sm text-neutral-700 hover:bg-primary-50 hover:text-primary-600 transition-all duration-200">
                        <i class="fas fa-shopping-bag w-5 mr-3 text-primary-500"></i>
                        <div>
                            <div class="font-medium">Đơn hàng của tôi</div>
                            <div class="text-xs text-neutral-500">Theo dõi đơn hàng</div>
                        </div>
                    </a>

                    <div class="border-t border-neutral-100 mt-2">
                        <form method="POST" action="{{ route('logout') }}" onsubmit="handleLogout()">
                            @csrf
                            <button type="submit" class="flex items-center w-full px-4 py-3 text-sm text-neutral-700 hover:bg-red-50 hover:text-red-600 transition-all duration-200">
                                <i class="fas fa-sign-out-alt w-5 mr-3 text-red-500"></i>
                                <div>
                                    <div class="font-medium">Đăng xuất</div>
                                    <div class="text-xs text-neutral-500">Thoát khỏi tài khoản</div>
                                </div>
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Guest User Menu -->
                    <div class="px-4 py-3 border-b border-neutral-100">
                        <p class="text-sm font-semibold text-neutral-900">Tài khoản</p>
                        <p class="text-xs text-neutral-500">Đăng nhập để trải nghiệm đầy đủ</p>
                    </div>

                    <a href="{{ route('login') }}"
                       class="flex items-center px-4 py-3 text-sm text-neutral-700 hover:bg-primary-50 hover:text-primary-600 transition-all duration-200">
                        <i class="fas fa-sign-in-alt w-5 mr-3 text-primary-500"></i>
                        <div>
                            <div class="font-medium">Đăng nhập</div>
                            <div class="text-xs text-neutral-500">Truy cập tài khoản của bạn</div>
                        </div>
                    </a>

                    <a href="{{ route('register') }}"
                       class="flex items-center px-4 py-3 text-sm text-neutral-700 hover:bg-primary-50 hover:text-primary-600 transition-all duration-200">
                        <i class="fas fa-user-plus w-5 mr-3 text-primary-500"></i>
                        <div>
                            <div class="font-medium">Đăng ký</div>
                            <div class="text-xs text-neutral-500">Tạo tài khoản mới</div>
                        </div>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Cart Button -->
    <button type="button"
            class="group relative p-3 rounded-xl hover:bg-primary-50 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
            data-drawer-target="drawer_cart"
            data-drawer-show="drawer_cart"
            data-drawer-backdrop="false"
            data-drawer-placement="right"
            aria-controls="drawer_cart"
            data-drawer-body-scrolling="true">
        <i class="fas fa-shopping-bag text-neutral-600 group-hover:text-primary-600 transition-colors duration-200 text-lg"></i>

        <!-- Cart Count Badge -->
        @if($cartCount > 0)
            <div class="absolute -top-1 -right-1 w-6 h-6 bg-primary-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-md border-2 border-white animate-pulse">
                {{ $cartCount > 99 ? '99+' : $cartCount }}
            </div>
        @endif

        <div class="absolute inset-0 rounded-xl bg-primary-500/10 scale-0 group-hover:scale-100 transition-transform duration-200"></div>
    </button>
</div>