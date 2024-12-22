<div>
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
                    <i data-dropdown-toggle="cart_pc" class="fa-solid fa-bag-shopping relative" >
                        <!-- stogge cart -->
                        <div id='cart_pc' class="absolute top-5 right-0 w-[50vw] h-[80vh] bg-red-500 font-sans hidden">
                            Giỏ hàng
                        </div>
                    </i>
                </div>
            </div>
        </div>
    </nav>

</div>
