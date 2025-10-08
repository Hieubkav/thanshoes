<!-- Mobile Menu Component - Separate from navbar to avoid z-index stacking issues -->
<style>
    /* Mobile menu styles - highest z-index to ensure it's always on top */
    #mobile-menu {
        will-change: transform;
        -webkit-transform: translateX(-100%);
        transform: translateX(-100%);
        z-index: 2147483647 !important; /* Maximum z-index value */
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
    }

    /* Optimized for smaller mobile screens */
    @media (max-width: 380px) {
        .mobile-sidebar-content {
            width: 85% !important;
            max-width: none !important;
        }
    }

    #mobile-menu-backdrop {
        background: rgba(0, 0, 0, 0.6) !important;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }

    /* Sidebar content */
    .mobile-sidebar-content {
        z-index: 2147483647 !important; /* Maximum z-index value */
        position: relative !important;
    }

    /* Ngăn body scroll khi menu mở */
    body.mobile-menu-open {
        overflow: hidden !important;
        position: fixed !important;
        width: 100% !important;
        height: 100% !important;
    }

    /* Dropdown icon rotation */
    .mobile-dropdown-icon {
        transition: transform 0.2s ease;
    }
    
    .mobile-dropdown-icon.rotate-180 {
        transform: rotate(180deg);
    }

    /* Animation keyframes */
    @keyframes dropdownSlideDown {
        from { max-height: 0; opacity: 0; }
        to { max-height: 500px; opacity: 1; }
    }
    
    .animate-dropdown {
        animation: dropdownSlideDown 0.3s ease-out forwards;
    }
</style>

<!-- Mobile Menu -->
<div id="mobile-menu" class="lg:hidden" style="display: none;">
    <!-- Backdrop Overlay -->
    <div id="mobile-menu-backdrop"></div>

    <!-- Sidebar Content -->
    <div class="mobile-sidebar-content w-3/4 sm:w-80 max-w-xs h-full bg-white dark:bg-gray-800 shadow-2xl overflow-y-auto">
        <!-- Modern Header -->
        <div class="p-4 sm:p-6 bg-gradient-to-r from-primary-50 to-accent border-b border-neutral-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-primary-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-bars text-white text-sm sm:text-base"></i>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-neutral-900">Menu</h2>
                        <p class="text-xs sm:text-sm text-neutral-600">Danh mục sản phẩm</p>
                    </div>
                </div>
                <button id="close-mobile-menu"
                        class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white/80 hover:bg-white text-neutral-500 hover:text-neutral-700 transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-md">
                    <i class="fas fa-times text-sm sm:text-lg"></i>
                </button>
            </div>
        </div>
        
        <nav class="p-3 sm:p-6">
            <ul class="space-y-2 sm:space-y-3">
                <!-- Mobile Menu Items -->
                <li>
                    <a href="{{ route('shop.cat_filter') }}"
                       class="flex items-center space-x-3 px-4 py-3 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-colors group @if(request()->get('phukien')) bg-primary-50 text-primary-600 @endif">
                        <i class="fas fa-th-large text-primary-500"></i>
                        <span class="font-medium">Tất cả sản phẩm</span>
                    </a>
                </li>
                
                <li class="mobile-dropdown">
                    <button class="w-full flex items-center justify-between px-4 py-3 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-colors group">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-tags text-primary-500"></i>
                            <span class="font-medium">Thương hiệu</span>
                        </div>
                        <i class="fas fa-chevron-down mobile-dropdown-icon text-neutral-400 transition-transform"></i>
                    </button>
                    <div class="hidden mobile-dropdown-content pl-8 mt-2 space-y-1">
                        @if(isset($brands))
                            @foreach ($brands as $brand)
                                <a href="{{ route('shop.cat_filter',['brand' => $brand]) }}"
                                   class="block px-4 py-2 text-neutral-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors @if(request()->get('brand') == $brand) bg-primary-50 text-primary-600 @endif">
                                    {{ $brand }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                </li>

                <li class="mobile-dropdown">
                    <button class="w-full flex items-center justify-between px-4 py-3 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-colors">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-shoe-prints text-primary-500"></i>
                            <span class="font-medium">Danh mục giày</span>
                        </div>
                        <i class="fas fa-chevron-down mobile-dropdown-icon text-neutral-400 transition-transform"></i>
                    </button>
                    <div class="hidden mobile-dropdown-content pl-8 mt-2 space-y-1">
                        @if(isset($types))
                            @foreach ($types as $type)
                                <a href="{{ route('shop.cat_filter',['type' => $type]) }}"
                                   class="block px-4 py-2 text-neutral-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors @if(request()->get('type') == $type) bg-primary-50 text-primary-600 @endif">
                                    {{ $type }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                </li>

                <li>
                    <a href="{{ route('shop.cat_filter',['tatvo' => 'true']) }}" 
                       class="flex items-center space-x-3 px-4 py-3 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-colors @if(request()->get('tatvo')) bg-primary-50 text-primary-600 @endif">
                        <i class="fas fa-socks text-primary-500"></i>
                        <span class="font-medium">Tất vớ, dép</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('shop.cat_filter',['phukien' => 'true']) }}" 
                       class="flex items-center space-x-3 px-4 py-3 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-colors @if(request()->get('phukien')) bg-primary-50 text-primary-600 @endif">
                        <i class="fas fa-gem text-primary-500"></i>
                        <span class="font-medium">Phụ kiện</span>
                    </a>
                </li>
                
                <li>
                    <a href="#contact" 
                       class="flex items-center space-x-3 px-4 py-3 text-neutral-700 hover:text-primary-600 hover:bg-primary-50 rounded-xl transition-colors">
                        <i class="fas fa-phone text-primary-500"></i>
                        <span class="font-medium">Liên hệ</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const closeMobileMenu = document.getElementById('close-mobile-menu');
    const backdrop = document.getElementById('mobile-menu-backdrop');
    const mobileDropdowns = document.querySelectorAll('.mobile-dropdown');

    // Kiểm tra các element tồn tại
    if (!mobileMenu || !mobileMenuButton || !closeMobileMenu || !backdrop) {
        console.warn('Mobile menu elements not found');
        return;
    }

    // Mở mobile menu
    function openMobileMenu() {
        mobileMenu.style.display = 'block';
        document.body.classList.add('mobile-menu-open');
        setTimeout(() => {
            mobileMenu.style.transform = 'translateX(0)';
        }, 10);
    }

    // Đóng mobile menu
    function closeMobileMenuHandler() {
        mobileMenu.style.transform = 'translateX(-100%)';
        document.body.classList.remove('mobile-menu-open');
        setTimeout(() => {
            mobileMenu.style.display = 'none';
        }, 300);
    }

    // Event listeners
    mobileMenuButton.addEventListener('click', openMobileMenu);
    closeMobileMenu.addEventListener('click', closeMobileMenuHandler);
    backdrop.addEventListener('click', closeMobileMenuHandler);

    // Xử lý mobile dropdowns - đơn giản hóa
    mobileDropdowns.forEach(dropdown => {
        const button = dropdown.querySelector('button');
        const content = dropdown.querySelector('.mobile-dropdown-content');
        const icon = dropdown.querySelector('.mobile-dropdown-icon');

        if (!button || !content || !icon) return;

        button.addEventListener('click', (e) => {
            e.preventDefault();
            const isHidden = content.classList.contains('hidden');
            
            // Đóng tất cả dropdown khác
            mobileDropdowns.forEach(other => {
                if (other !== dropdown) {
                    other.querySelector('.mobile-dropdown-content')?.classList.add('hidden');
                    other.querySelector('.mobile-dropdown-icon')?.classList.remove('rotate-180');
                }
            });

            // Toggle dropdown hiện tại
            content.classList.toggle('hidden', !isHidden);
            icon.classList.toggle('rotate-180', isHidden);
        });
    });

    // Đóng menu khi resize hoặc nhấn ESC
    window.addEventListener('resize', () => window.innerWidth >= 1024 && closeMobileMenuHandler());
    document.addEventListener('keydown', (e) => e.key === 'Escape' && closeMobileMenuHandler());
});
</script>
