{{-- <div>
    <nav
        class="bg-white border-gray-200 dark:border-gray-600 dark:bg-gray-900 z-50 fixed top-0 right-0 left-0 shadow-md">
        @include('component.topbar')

        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl p-1">
            <!-- Logo -->
            <a href="{{ route('shop.store_front') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="{{ asset('images/logo.svg') }}" class="h-16" alt="Logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white hidden lg:block">
                    {{ env('APP_NAME') }}
                </span>
            </a>

            <!-- Menu trái -->
            <button data-collapse-toggle="mega-menu-full" type="button"
                class="order-3 inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                aria-controls="mega-menu-full" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <i class="fa fa-bars w-5 h-5  hover:bg-gray-200 hover:text-blue-600 rounded-full"
                    aria-hidden="true"></i>
            </button>


            <!-- Menu giữa-->
            <div id="mega-menu-full" class="items-center justify-between hidden w-full md:flex md:w-auto md:order-2">
                <ul class="flex flex-col mt-4 font-medium md:flex-row md:mt-0 md:space-x-8 rtl:space-x-reverse">
                    <li>
                        <button id="mega-menu-full-dropdown-all-product-button"
                            data-dropdown-toggle="mega-menu-full-dropdown-all-product"
                            class="flex items-center justify-between w-full py-2 px-3 font-medium text-gray-900 border-b border-gray-100 md:w-auto hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-600 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700">
                            Thương hiệu
                            <i class="fa fa-chevron-down ms-3"></i>
                        </button>
                        <!-- Dropdown thương hiệu -->
                        <div id="mega-menu-full-dropdown-all-product"
                            class="hidden mt-1 bg-white border-gray-200 shadow-sm border-y dark:bg-gray-800 dark:border-gray-600 w-full">
                            <ul
                                class="grid max-w-screen-xl px-4 py-5 mx-auto text-gray-900 dark:text-white sm:grid-cols-2 md:grid-cols-3 md:px-6">
                                @foreach ($brands as $brand)
                                    <li>
                                        <a href="#"
                                            class="block p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-200 ease-in-out">
                                            <div class="font-semibold text-center text-gray-900 dark:text-white">
                                                {{ $brand }}
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    <li>
                        <button id="list-shoes-button" data-dropdown-toggle="list-shoes"
                            class="flex items-center justify-between w-full py-2 px-3 font-medium text-gray-900 border-b border-gray-100 md:w-auto hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-600 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700">
                            Danh mục giày
                            <i class="fa fa-chevron-down ms-3"></i>
                        </button>
                        <!-- Dropdown danh mục -->
                        <div id="list-shoes"
                            class="hidden mt-1 bg-white border-gray-200 shadow-sm border-y dark:bg-gray-800 dark:border-gray-600 w-full">
                            <ul
                                class="grid max-w-screen-xl px-4 py-5 mx-auto text-gray-900 dark:text-white sm:grid-cols-2 md:grid-cols-3 md:px-6">
                                @foreach ($types as $type)
                                    <li>
                                        <a href="#"
                                            class="block p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-200 ease-in-out">
                                            <div class="font-semibold text-center text-gray-900 dark:text-white">
                                                {{ $type }}
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="#"
                            class="block py-2 px-3 text-gray-900 border-b border-gray-100 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700">
                            Các mẫu áo
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="block py-2 px-3 text-gray-900 border-b border-gray-100 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700">
                            Phụ kiện
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="block py-2 px-3 text-gray-900 border-b border-gray-100 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-blue-500 md:dark:hover:bg-transparent dark:border-gray-700">
                            Liên hệ
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Icon search, user, cart -->
            <div class="md:order-3 order-1 hidden md:grid grid-cols-3  gap-4">
                <!-- Icon search -->
                <div class="col-span-1 cursor-pointer text-xl">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>

                <!-- Icon user -->
                <div class="col-span-1 cursor-pointer text-xl">
                    <i class="fa-solid fa-user"></i>
                </div>

                <!-- Icon cart -->
                <div class="col-span-1 cursor-pointer text-xl">
                    <i class="fa-solid fa-shopping-cart" data-drawer-target="drawer_cart" data-drawer-show="drawer_cart"
                        aria-controls="drawer_cart">
                    </i>

                    <div id="drawer_cart"
                        class="fixed top-0 left-0 z-40 h-screen w-64 transition-transform translate-x-full bg-red-500 shadow-lg"
                        tabindex="-1" aria-labelledby="drawer-title" aria-hidden="true">
                        <div class="p-4">
                            <h5 id="drawer-title" class="text-lg font-bold">Tiêu đề Drawer</h5>
                            <button type="button" data-drawer-hide="drawer_cart" aria-controls="drawer_cart"
                                class="absolute top-2 right-2 p-2 text-gray-500 hover:text-gray-700">
                                Đóng
                            </button>
                            <div class="mt-4">
                                <!-- Nội dung Drawer -->
                                Đây là nội dung của Drawer.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </nav>

</div> --}}


<div>
    <nav
        class="bg-white border-gray-200 dark:border-gray-600 dark:bg-gray-900 fixed top-0 right-0 left-0 shadow-md z-50">
        <!-- Topbar -->
        @include('component.topbar')

        <!-- Container -->
        <div class="flex flex-wrap items-center justify-between mx-auto max-w-screen-xl p-4">
            <!-- Logo -->
            <a href="{{ route('shop.store_front') }}" class="flex items-center space-x-3">
                <img src="{{ asset('images/logo.svg') }}" class="h-16" alt="Logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white hidden lg:block">
                    {{ env('APP_NAME') }}
                </span>
            </a>

            <!-- Mobile Menu Button -->
            <button data-collapse-toggle="mega-menu-full" type="button"
                class="md:hidden inline-flex items-center justify-center p-2 text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                <i class="fa fa-bars w-5 h-5" aria-hidden="true"></i>
            </button>

            <!-- Navigation -->
            <div id="mega-menu-full" class="hidden w-full md:flex md:w-auto">
                <ul class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-8 font-medium">
                    <li>
                        <button data-dropdown-toggle="mega-menu-full-dropdown-all-product"
                            class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-500">
                            Thương hiệu <i class="fa fa-chevron-down"></i>
                        </button>
                        <div id="mega-menu-full-dropdown-all-product"
                            class="hidden mt-2 bg-white shadow-lg border dark:bg-gray-800 dark:border-gray-600">
                            <ul class="grid grid-cols-2 sm:grid-cols-3 gap-4 p-4">
                                @foreach ($brands as $brand)
                                    <li>
                                        <a href="#"
                                            class="block p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                            {{ $brand }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    <li>
                        <button data-dropdown-toggle="list-shoes"
                            class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-500">
                            Danh mục giày <i class="fa fa-chevron-down"></i>
                        </button>
                        <div id="list-shoes"
                            class="hidden mt-2 bg-white shadow-lg border dark:bg-gray-800 dark:border-gray-600">
                            <ul class="grid grid-cols-2 sm:grid-cols-3 gap-4 p-4">
                                @foreach ($types as $type)
                                    <li>
                                        <a href="#"
                                            class="block p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                            {{ $type }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    <li><a href="#" class="hover:text-blue-600 dark:hover:text-blue-500">Các mẫu áo</a></li>
                    <li><a href="#" class="hover:text-blue-600 dark:hover:text-blue-500">Phụ kiện</a></li>
                    <li><a href="#" class="hover:text-blue-600 dark:hover:text-blue-500">Liên hệ</a></li>
                </ul>
            </div>

            <!-- Icons (Search, User, Cart) -->
            <div class="flex space-x-4">
                <!-- Search -->
                <div class="text-xl cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>

                <!-- User -->
                <div class="text-xl cursor-pointer">
                    <i class="fa-solid fa-user"></i>
                </div>

                <!-- Cart -->
                <div class="text-xl cursor-pointer">
                    <i class="fa-solid fa-shopping-cart" data-drawer-target="drawer_cart" data-drawer-show="drawer_cart"
                        data-drawer-placement="right" aria-controls="drawer_cart"></i>

                    <!-- Drawer -->
                    <div id="drawer_cart"
                        class="fixed top-0 right-0 z-40 h-screen w-80 transition-transform translate-x-full bg-white shadow-lg dark:bg-gray-900"
                        tabindex="-1" aria-labelledby="drawer-title" aria-hidden="true">
                        <div class="p-4 flex flex-col h-full">
                            <!-- Tiêu đề và nút đóng -->
                            <div class="flex justify-between items-center border-b pb-2">
                                <h5 id="drawer-title" class="text-lg font-bold text-gray-900 dark:text-white">Giỏ hàng
                                </h5>
                                <button type="button" data-drawer-hide="drawer_cart" aria-controls="drawer_cart"
                                    class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>

                            <!-- Nội dung giỏ hàng -->
                            <div class="flex-1 overflow-y-auto mt-4">
                                <!-- Sản phẩm trong giỏ -->
                                <div class="flex items-center border-b py-3">
                                    <img src="https://via.placeholder.com/50" alt="Product Image"
                                        class="w-16 h-16 rounded-md object-cover">
                                    <div class="ml-3 flex-1">
                                        <h5 class="font-medium text-gray-800 dark:text-white">Tên sản phẩm</h5>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Số lượng: 1</p>
                                    </div>
                                    <span class="font-semibold text-gray-900 dark:text-white">150.000đ</span>
                                </div>
                                <!-- Thêm sản phẩm -->
                                <div class="flex items-center border-b py-3">
                                    <img src="https://via.placeholder.com/50" alt="Product Image"
                                        class="w-16 h-16 rounded-md object-cover">
                                    <div class="ml-3 flex-1">
                                        <h5 class="font-medium text-gray-800 dark:text-white">Tên sản phẩm khác</h5>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Số lượng: 2</p>
                                    </div>
                                    <span class="font-semibold text-gray-900 dark:text-white">300.000đ</span>
                                </div>
                            </div>

                            <!-- Tổng tiền và nút thanh toán -->
                            <div class="border-t pt-4">
                                <div class="flex justify-between mb-3">
                                    <span class="text-gray-500 dark:text-gray-400">Tổng cộng:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">450.000đ</span>
                                </div>
                                <button
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                    Thanh toán
                                </button>
                                <button
                                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-lg mt-2 transition duration-200 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-white">
                                    Tiếp tục mua sắm
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </nav>
</div>
