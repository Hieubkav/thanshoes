<!-- Navigation -->
<style>
    @keyframes slideIn {
        from { transform: translateX(-100%); }
        to { transform: translateX(0); }
    }
    @keyframes slideOut {
        from { transform: translateX(0); }
        to { transform: translateX(-100%); }
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes dropdownSlideDown {
        from { max-height: 0; opacity: 0; }
        to { max-height: 500px; opacity: 1; }
    }
    .animate-slide-in {
        animation: slideIn 0.3s ease-out;
    }
    .animate-slide-out {
        animation: slideOut 0.3s ease-out;
    }
    .animate-dropdown {
        animation: dropdownSlideDown 0.3s ease-out forwards;
    }
    .active-link {
        @apply bg-gray-100 dark:bg-gray-700 text-blue-600 dark:text-blue-400;
    }
    .animate-fade-in {
        animation: fadeIn 0.2s ease-out;
    }
    .backdrop {
        background: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(4px);
    }
</style>

<div class="relative flex items-center justify-between w-full lg:w-auto">
    <!-- Mobile Menu Button -->
    <button id="mobile-menu-button" type="button"
            class="lg:hidden inline-flex items-center p-2 text-gray-700 hover:text-gray-900 dark:text-gray-200 dark:hover:text-white focus:outline-none">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
        </svg>
    </button>

    <!-- Mobile Sidebar -->
    <div id="mobile-menu" 
         class="fixed inset-0 z-50 lg:hidden translate-x-[-100%] transition-transform duration-300 ease-in-out">
        <!-- Backdrop -->
        <div class="backdrop absolute inset-0"></div>
        
        <!-- Sidebar Content -->
        <div class="relative w-3/4 max-w-xs h-full bg-white dark:bg-gray-800 shadow-xl overflow-y-auto">
            <div class="p-4 border-b dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Menu</h2>
                    <button id="close-mobile-menu" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline-none">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <nav class="p-4">
                <ul class="space-y-2">
                    <!-- Mobile Menu Items -->
                    <li>
                        <a href="{{ route('shop.cat_filter') }}" 
                           @if(request()->get('phukien')) class="active-link block p-2.5 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg transition-all duration-200" @else class="block p-2.5 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg transition-all duration-200" @endif>
                            Tất cả sản phẩm
                        </a>
                    </li>
                    <li class="mobile-dropdown">
                        <button class="w-full flex items-center justify-between p-2.5 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg transition-all duration-200">
                            <span>Thương hiệu</span>
                            <svg class="w-4 h-4 transition-transform mobile-dropdown-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="hidden mobile-dropdown-content pl-3 mt-1 space-y-1 overflow-hidden">
                            @foreach ($brands as $brand)
                                <a href="{{ route('shop.cat_filter',['brand' => $brand]) }}"
 
                                   @if(request()->get('brand') == $brand) 
                                   class="active-link block p-2.5 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded-lg transition-all duration-200"
                                   @else
                                   class="block p-2.5 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded-lg transition-all duration-200"
                                   @endif>
                                    {{ $brand }}
                                </a>
                            @endforeach
                        </div>
                    </li>

                    <li class="mobile-dropdown">
                        <button class="w-full flex items-center justify-between p-2.5 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg transition-all duration-200">
                            <span>Danh mục giày</span>
                            <svg class="w-4 h-4 transition-transform mobile-dropdown-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="hidden mobile-dropdown-content pl-3 mt-1 space-y-1 overflow-hidden">
                            @foreach ($types as $type)
                                <a href="{{ route('shop.cat_filter',['type' => $type]) }}"
 
                                   @if(request()->get('type') == $type) class="active-link block p-2.5 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded-lg transition-all duration-200" @else class="block p-2.5 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded-lg transition-all duration-200" @endif>
                                    {{ $type }}
                                </a>
                            @endforeach
                        </div>
                    </li>

                    <li>
                        <a href="{{ route('shop.cat_filter',['tatvo' => 'true']) }}" 
                           @if(request()->get('tatvo')) class="active-link block p-2.5 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg transition-all duration-200" @else class="block p-2.5 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg transition-all duration-200" @endif>
                            Tất vớ, dép
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('shop.cat_filter',['phukien' => 'true']) }}" 
                           @if(request()->get('phukien')) class="active-link block p-2.5 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg transition-all duration-200" @else class="block p-2.5 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg transition-all duration-200" @endif>
                            Phụ kiện
                        </a>
                    </li>
                    <li>
                        <a href="#contact" 
                           class="block p-2.5 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg transition-all duration-200">
                            Liên hệ
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Desktop Navigation -->
    <nav class="hidden lg:block">
        <ul class="flex items-center space-x-8">
            <li>
                <a href="{{ route('shop.cat_filter') }}" 
                   class="py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    Tất cả sản phẩm
                </a>
            </li>
            <!-- Desktop Dropdown: Thương hiệu -->
            <li class="relative group">
                <button class="flex items-center space-x-1 py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    <span>Thương hiệu</span>
                    <svg class="w-4 h-4 group-hover:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="invisible group-hover:visible opacity-0 group-hover:opacity-100 absolute left-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-100 dark:border-gray-700 transition-all duration-200 ease-in-out">
                    <div class="p-4 grid grid-cols-2 gap-2">
                        @foreach ($brands as $brand)
                            <a href="{{ route('shop.cat_filter',['brand' => $brand]) }}"
                               class="px-4 py-2 text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                {{ $brand }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </li>

            <!-- Desktop Dropdown: Danh mục giày -->
            <li class="relative group">
                <button class="flex items-center space-x-1 py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    <span>Danh mục giày</span>
                    <svg class="w-4 h-4 group-hover:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="invisible group-hover:visible opacity-0 group-hover:opacity-100 absolute left-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-100 dark:border-gray-700 transition-all duration-200 ease-in-out">
                    <div class="p-4 grid grid-cols-2 gap-2">
                        @foreach ($types as $type)
                            <a href="{{ route('shop.cat_filter',['type' => $type]) }}"
                               class="px-4 py-2 text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                {{ $type }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </li>

            <!-- Desktop Regular Links -->
            <li>
                <a href="{{ route('shop.cat_filter',['tatvo' => 'true']) }}" 
                   class="py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    Tất vớ, dép
                </a>
            </li>
            <li>
                <a href="{{ route('shop.cat_filter',['phukien' => 'true']) }}" 
                   class="py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    Phụ kiện
                </a>
            </li>
            <li>
                <a href="#" 
                   class="py-2 text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    Liên hệ
                </a>
            </li>
        </ul>
    </nav>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const closeMobileMenu = document.getElementById('close-mobile-menu');
    const backdrop = mobileMenu.querySelector('.backdrop');
    const mobileDropdowns = document.querySelectorAll('.mobile-dropdown');

    // Open mobile menu
    mobileMenuButton.addEventListener('click', () => {
        mobileMenu.style.transform = 'translateX(0)';
        document.body.style.overflow = 'hidden';
    });

    // Close mobile menu
    function closeMobileMenuHandler() {
        mobileMenu.style.transform = 'translateX(-100%)';
        document.body.style.overflow = '';
    }

    closeMobileMenu.addEventListener('click', closeMobileMenuHandler);
    backdrop.addEventListener('click', closeMobileMenuHandler);

    // Handle mobile dropdowns
    mobileDropdowns.forEach(dropdown => {
        const button = dropdown.querySelector('button');
        const content = dropdown.querySelector('.mobile-dropdown-content');
        const icon = dropdown.querySelector('.mobile-dropdown-icon');

        button.addEventListener('click', () => {
            const isExpanded = content.classList.contains('hidden');
            
            // Close all other dropdowns
            mobileDropdowns.forEach(otherDropdown => {
                if (otherDropdown !== dropdown) {
                    otherDropdown.querySelector('.mobile-dropdown-content').classList.add('hidden');
                    otherDropdown.querySelector('.mobile-dropdown-icon').style.transform = 'rotate(0deg)';
                }
            });

            // Toggle current dropdown
            if (isExpanded) {
                content.classList.add('animate-dropdown');
            }
            content.classList.toggle('hidden');
            icon.style.transform = isExpanded ? 'rotate(180deg)' : 'rotate(0deg)';
            
            content.addEventListener('animationend', () => content.classList.remove('animate-dropdown'));
        });
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) { // lg breakpoint
            mobileMenu.style.transform = 'translateX(-100%)';
            document.body.style.overflow = '';
        }
    });
});
</script>