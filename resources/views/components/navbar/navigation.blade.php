<!-- Desktop Navigation Only -->
<div class="relative w-full">

    <!-- Desktop Navigation -->
    <nav class="hidden lg:block py-3">
        <ul class="flex items-center justify-center space-x-8">
            <li>
                <a href="{{ route('shop.cat_filter') }}"
                   class="nav-link py-2 px-3 rounded-lg hover:bg-primary-50 transition-all duration-200 font-medium">
                    <i class="fas fa-th-large mr-2 text-primary-500"></i>
                    Tất cả sản phẩm
                </a>
            </li>
            <!-- Desktop Dropdown: Thương hiệu -->
            <li class="relative group">
                <button class="nav-link flex items-center space-x-2 py-2 px-3 rounded-lg hover:bg-primary-50 transition-all duration-200 font-medium">
                    <i class="fas fa-tags text-primary-500"></i>
                    <span>Thương hiệu</span>
                    <svg class="w-4 h-4 group-hover:rotate-180 transition-transform text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="invisible group-hover:visible opacity-0 group-hover:opacity-100 absolute left-0 mt-2 w-72 bg-white rounded-xl shadow-soft-lg border border-neutral-200/50 transition-all duration-200 ease-in-out z-50">
                    <div class="p-4">
                        <h4 class="text-sm font-semibold text-neutral-700 mb-3 flex items-center">
                            <i class="fas fa-star text-primary-500 mr-2"></i>
                            Thương hiệu nổi bật
                        </h4>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($brands as $brand)
                                <a href="{{ route('shop.cat_filter',['brand' => $brand]) }}"
                                   class="flex items-center px-3 py-2 text-neutral-700 hover:text-primary-600 rounded-lg hover:bg-primary-50 transition-all duration-200 group/item">
                                    <i class="fas fa-circle text-primary-300 text-xs mr-2 group-hover/item:text-primary-500"></i>
                                    {{ $brand }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </li>

            <!-- Desktop Dropdown: Danh mục giày -->
            <li class="relative group">
                <button class="nav-link flex items-center space-x-2 py-2 px-3 rounded-lg hover:bg-primary-50 transition-all duration-200 font-medium">
                    <i class="fas fa-shoe-prints text-primary-500"></i>
                    <span>Danh mục giày</span>
                    <svg class="w-4 h-4 group-hover:rotate-180 transition-transform text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="invisible group-hover:visible opacity-0 group-hover:opacity-100 absolute left-0 mt-2 w-80 bg-white rounded-xl shadow-soft-lg border border-neutral-200/50 transition-all duration-200 ease-in-out z-50">
                    <div class="p-4">
                        <h4 class="text-sm font-semibold text-neutral-700 mb-3 flex items-center">
                            <i class="fas fa-list text-primary-500 mr-2"></i>
                            Danh mục sản phẩm
                        </h4>
                        <div class="grid grid-cols-2 gap-2 max-h-64 overflow-y-auto">
                            @foreach ($types as $type)
                                <a href="{{ route('shop.cat_filter',['type' => $type]) }}"
                                   class="flex items-center px-3 py-2 text-neutral-700 hover:text-primary-600 rounded-lg hover:bg-primary-50 transition-all duration-200 group/item">
                                    <i class="fas fa-arrow-right text-primary-300 text-xs mr-2 group-hover/item:text-primary-500 group-hover/item:translate-x-1 transition-all duration-200"></i>
                                    {{ $type }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </li>

            <!-- Desktop Regular Links -->
            <li>
                <a href="{{ route('shop.cat_filter',['tatvo' => 'true']) }}"
                   class="nav-link py-2 px-3 rounded-lg hover:bg-primary-50 transition-all duration-200 font-medium">
                    <i class="fas fa-socks mr-2 text-primary-500"></i>
                    Tất vớ, dép
                </a>
            </li>
            <li>
                <a href="{{ route('shop.cat_filter',['phukien' => 'true']) }}"
                   class="nav-link py-2 px-3 rounded-lg hover:bg-primary-50 transition-all duration-200 font-medium">
                    <i class="fas fa-gem mr-2 text-primary-500"></i>
                    Phụ kiện
                </a>
            </li>
            <li>
                <a href="#"
                   class="nav-link py-2 px-3 rounded-lg hover:bg-primary-50 transition-all duration-200 font-medium">
                    <i class="fas fa-phone mr-2 text-primary-500"></i>
                    Liên hệ
                </a>
            </li>
        </ul>
    </nav>
</div>