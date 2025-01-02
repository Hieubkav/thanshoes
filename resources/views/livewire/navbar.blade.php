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
                    <i class="fa-solid fa-user" data-dropdown-toggle="dropdown_user"></i>
                    <div id="dropdown_user"
                        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                            aria-labelledby="dropdownDefaultButton">
                            <li>
                                <a href="#" data-modal-target="info_user_modal"
                                    data-modal-toggle="info_user_modal"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Thông tin
                                </a>
                            </li>
                            <li>
                                <a href="#" data-modal-target="info_user_order" data-modal-toggle="info_user_order"
                                    class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    Đơn đặt
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Cart -->
                <div class="text-xl cursor-pointer">
                    <i class="fa-solid fa-shopping-cart relative" data-drawer-target="drawer_cart"
                        data-drawer-show="drawer_cart" data-drawer-backdrop="false" data-drawer-placement="right"
                        aria-controls="drawer_cart" data-drawer-body-scrolling="true">
                        <div
                            class="absolute inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-orange-700 p-2 border-2 border-white rounded-full -top-3 -end-3 dark:border-gray-900">
                            {{ collect($cart)->sum('quantity') }}
                        </div>
                    </i>

                    <!-- Drawer -->
                    <div id="drawer_cart"
                        class="fixed top-0 right-0 z-40 h-screen w-80 transition-transform translate-x-full bg-gradient-to-r from-orange-100 via-gray-100 to-blue-100  shadow-lg dark:bg-gray-900"
                        tabindex="-1" aria-labelledby="drawer-title" aria-hidden="true">
                        <div class="p-4 flex flex-col h-full">
                            <!-- Tiêu đề và nút đóng -->
                            <div class="flex justify-between items-center border-b pb-0">
                                <h5 id="drawer-title" class="text-lg font-bold text-gray-900 dark:text-white">
                                    Giỏ Hàng/Thanh Toán
                                </h5>
                                <button type="button" data-drawer-hide="drawer_cart"
                                    class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>

                            <!-- Chữ clear all ấn vào xoá toàn bộ giỏ hàng -->
                            <div class="flex justify-end py-2" wire:click='clear_cart'>
                                <button class="text-sm text-red-600 dark:text-red-400 hover:underline">Xoá giỏ</button>
                            </div>

                            <!-- Nội dung giỏ hàng -->
                            <div class="flex-1 overflow-y-auto mt-0 text-sm">
                                @forelse ($cart as $item)
                                    <div class="flex items-center border-b py-1">
                                        <div class="relative inline-block">
                                            <!-- Hình ảnh -->
                                            <img src="{{ $item['image'] }}" alt="Product Image"
                                                class="w-6 h-6 lg:w-8 lg:h-8 rounded-md object-cover">
                                            <!-- Badge -->
                                            <span
                                                class="absolute text-xs top-0 right-0 inline-flex items-center justify-center px-1 py-0 font-bold leading-none text-red-100 bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2 shadow-lg">
                                                {{ $item['quantity'] }}
                                            </span>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <h5 class="font-medium text-gray-800 dark:text-white">
                                                {{ $item['product_name'] }}</h5>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Phiên bản:
                                                {{ $item['variant_color'] }}/{{ $item['variant_size'] }}</p>
                                        </div>
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ number_format($item['price'], 0, ',', '.') }}đ <br>
                                            {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ
                                        </span>
                                    </div>
                                @empty
                                    <div class="flex flex-col items-center justify-center py-10">
                                        <i class="fa-solid fa-cart-arrow-down text-4xl text-gray-400 mb-4"></i>
                                        <p class="text-gray-500 dark:text-gray-400">Giỏ hàng trống</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Thông tin khách hàng -->
                            <div class="border-t pt-4 text-sm grid grid-cols-2 gap-1">
                                <div class="mb-1">
                                    <label for="name" class="block text-gray-700 dark:text-gray-300">Tên:</label>
                                    <input type="text" id="name" wire:model="name_customer"
                                        class="w-full p-0 border rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                </div>
                                <div class="mb-1">
                                    <label for="phone" class="block text-gray-700 dark:text-gray-300">Số điện
                                        thoại:</label>
                                    <input type="text" id="phone" wire:model="phone_customer"
                                        class="w-full p-0 border rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                </div>
                                <div class="mb-1">
                                    <label for="email"
                                        class="block text-gray-700 dark:text-gray-300">Email:</label>
                                    <input type="email" id="email" wire:model="email_customer"
                                        class="w-full p-0 border rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                </div>
                                <div class="mb-1">
                                    <label for="address" class="block text-gray-700 dark:text-gray-300">Địa
                                        chỉ:</label>
                                    <input type="text" id="address" wire:model="address_customer"
                                        class="w-full p-0 border rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                </div>
                            </div>

                            <!-- Hình thức thanh toán -->
                            <div class="border-t pt-4 text-sm">
                                <span class="block text-gray-700 dark:text-gray-300 mb-2 font-extrabold">Hình thức
                                    thanh toán:</span>
                                <div class="flex space-x-4">
                                    <label class="flex items-center text-gray-700 dark:text-gray-300">
                                        <input type="radio" id="cod" name="payment_method" value="cod"
                                            class="mr-2" wire:model="payment_method" checked>
                                        Thanh toán khi nhận hàng (COD)
                                    </label>
                                    <label class="flex items-center text-gray-700 dark:text-gray-300">
                                        <input type="radio" id="bank_transfer" name="payment_method"
                                            value="bank_transfer" class="mr-2" wire:model="payment_method">
                                        Chuyển khoản
                                    </label>
                                </div>
                            </div>

                            <!-- Tổng tiền và nút thanh toán -->
                            <div class="border-t pt-4">
                                <div class="flex justify-between mb-3">
                                    <span class="text-gray-500 dark:text-gray-400">Tổng cộng:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ number_format(array_reduce($cart, fn($total, $item) => $total + $item['quantity'] * $item['price'], 0), 0, ',', '.') }}đ
                                    </span>
                                </div>
                                <div class="relative">
                                    <button wire:click="dat_hang()"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                        Đặt hàng
                                    </button>
                                    <span
                                        class="absolute top-0 -left-8 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-green-600 rounded-full transform translate-x-1/2 -translate-y-1/2 shadow-lg">
                                        Free Ship
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </nav>

    <!-- Modal thông tin -->
    <div id="info_user_modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Thông tin khách hàng
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="info_user_modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4 text-gray-700 dark:text-gray-300">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-user text-blue-600"></i>
                        <span class="font-semibold">Tên khách hàng:</span>
                        <span>{{ $name_customer }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-phone text-green-600"></i>
                        <span class="font-semibold">Số điện thoại:</span>
                        <span>{{ $phone_customer }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-envelope text-red-600"></i>
                        <span class="font-semibold">Email:</span>
                        <span>{{ $email_customer }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-map-marker-alt text-yellow-600"></i>
                        <span class="font-semibold">Địa chỉ:</span>
                        <span>{{ $address_customer }}</span>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    
                    <button data-modal-hide="info_user_modal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Danh sách đơn đã dặt -->
    <div id="info_user_order" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Thông tin đơn đã đặt
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="info_user_order">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4 text-gray-700 dark:text-gray-300">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                        STT
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                        Mã đơn
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                        Tổng tiền
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                        Tổng số món
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700 ">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        1
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        OD123456
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        1,000,000đ
                                    </td>
                                    <td class="flex justify-between px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        3
                                        <!-- icon mũi tên chỉ xuống trong  font awesome -->
                                        <i class="fa fa-chevron-down cursor-pointer hover:text-blue-500" data-dropdown-toggle="dropdownId" aria-hidden="true"></i>
                                        
                                    </td>
                                    
                                </tr>
                                
                            </tbody>
                        </table>
                        
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="info_user_order" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>


</div>
