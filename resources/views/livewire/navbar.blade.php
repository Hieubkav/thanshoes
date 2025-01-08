<div>
    <nav
        class="bg-white border-gray-200 dark:border-gray-600 dark:bg-gray-900 fixed top-0 right-0 left-0 shadow-md z-50">
        <!-- Topbar -->
        @include('component.topbar')

        <!-- Container -->
        <div class="flex flex-wrap items-center justify-between mx-auto max-w-screen-xl p-4">
            <!-- Logo -->
            <a href="{{ route('shop.store_front') }}" class="flex items-center space-x-3">
                <img src="{{ asset('images/logo.svg') }}" class="h-16" alt="Logo"/>
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
                                        <a href="{{ route('shop.cat_filter',['brand' => $brand]) }}"
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
                                        <a href="{{ route('shop.cat_filter',['type' => $type]) }}"
                                           class="block p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                            {{ $type }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    <li><a href="{{ route('shop.cat_filter',['tatvo' => 'true']) }}" class="hover:text-blue-600 dark:hover:text-blue-500">Tất vớ,dép</a></li>
                    <li><a href="{{ route('shop.cat_filter',['phukien' => 'true']) }}" class="hover:text-blue-600 dark:hover:text-blue-500">Phụ kiện</a></li>
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
                         class="fixed top-0 right-0 z-40 h-screen w-3/4 lg:w-1/5 transition-transform translate-x-full bg-gradient-to-r from-orange-100 via-gray-100 to-blue-100  shadow-lg dark:bg-gray-900"
                         tabindex="-1" aria-labelledby="drawer-title" aria-hidden="true">
                        <div class="p-4 flex flex-col h-full">
                            <!-- Tiêu đề và nút đóng -->
                            <div class="flex justify-between items-center border-b pb-0">
                                <h5 id="drawer-title" class="text-lg font-bold text-gray-900 dark:text-white">
                                    Giỏ Hàng/Thanh Toán
                                    {{--                                    @if($qrCodeSvg)--}}
                                    {{--                                        <img class="h-16 w-16" src="data:image/svg+xml;base64,{{ $qrCodeSvg }}" alt="QR Code">--}}
                                    {{--                                    @endif--}}
                                    <img class="w-1/2"
                                         src="https://img.vietqr.io/image/970437-{{ $accountNumber }}-compact2.jpg?amount={{ array_reduce($cart, fn($total, $item) => $total + $item['quantity'] * $item['price'], 0)  }}&addInfo=Đơn giày ThanShoes Chất Lượng!&accountName={{ $accountHolder  }}"
                                         alt="Thanh toán">
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
                                           class="block text-gray-700 dark:text-gray-300">
                                        Email
                                        <span class="italic text-xs text-green-400">(Có thể trống):</span>
                                    </label>
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
                                               value="bank" class="mr-2" wire:model="payment_method">
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
                                            wire:loading.attr="disabled"
                                            wire:target="dat_hang"
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                                        <span wire:loading.remove wire:target="dat_hang">Đặt hàng</span>
                                        <span wire:loading wire:target="dat_hang"
                                              class="flex items-center justify-center">
                                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                     viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Đang xử lý
                                                <span class="dots ml-1"></span>
                                        </span>
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

    <!-- Modal thông tin user -->
    <div id="info_user_modal" tabindex="-1" aria-hidden="true"
         class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-screen bg-black bg-opacity-50">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div
                class="relative bg-white rounded-lg shadow-xl dark:bg-gray-800 transform transition-all duration-300 ease-in-out">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">
                        Thông tin khách hàng
                    </h3>
                    <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="info_user_modal">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                  clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6 text-gray-700 dark:text-gray-300">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-user-circle text-3xl text-blue-600"></i>
                        <div>
                            <p class="font-semibold">Tên khách hàng</p>
                            <p>{{ $name_customer }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-phone-alt text-3xl text-green-600"></i>
                        <div>
                            <p class="font-semibold">Số điện thoại</p>
                            <p>{{ $phone_customer }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-envelope text-3xl text-red-600"></i>
                        <div>
                            <p class="font-semibold">Email</p>
                            <p>{{ $email_customer }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-map-marker-alt text-3xl text-yellow-600"></i>
                        <div>
                            <p class="font-semibold">Địa chỉ</p>
                            <p>{{ $address_customer }}</p>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div
                    class="flex items-center justify-between p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <div class="flex items-center">
                        <label for="save-info" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Bạn đặt
                            hàng thông tin sẽ tự động được lưu</label>
                    </div>
                    <button data-modal-hide="info_user_modal" type="button"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal đơn đã  đặt -->
    <div id="info_user_order" tabindex="-1" aria-hidden="true"
         class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
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
                                  stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
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
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    STT
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Tổng số món
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Tổng tiền
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Ngày đặt
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Chi tiết
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @if ($order->count() > 0)
                                @foreach( $order as $i => $order_in_list  )
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $i+1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $order_in_list->order_items->sum('quantity')   }} món
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ number_format($order_in_list->order_items->sum('price')) }}đ
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $order_in_list->created_at->format('d/m/Y')  }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            <button
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-600"
                                                onclick="toggleDetails({{ $i }})">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr id="details-{{ $i }}" class="hidden bg-gray-50 dark:bg-gray-700">
                                        <td colspan="5" class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                <p><strong>Sản phẩm:</strong></p>
                                                <ul class="list-disc list-inside">
                                                    @foreach( $order_in_list->order_items as $j => $order_item )
                                                        <li>Sản
                                                            phẩm: {{$order_item->variant->product->name}} -
                                                            phiên
                                                            bản: {{ $order_item->variant->color }}
                                                            /{{ $order_item->variant->size }} - Số
                                                            lượng: {{ $order_item->quantity }} -
                                                            Giá 1 sản
                                                            phẩm: {{ number_format($order_item->price) }}
                                                            đ -
                                                            Tổng
                                                            giá: {{ number_format($order_item->price*$order_item->quantity) }}
                                                            đ
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <p><strong>Trạng thái:</strong>
                                                    @php
                                                        if ($order_in_list->status === 'pending') {
                                                            echo 'Đang chờ xử lý';
                                                        } else if ($order_in_list->status === 'processing') {
                                                            echo 'Đã xử lý';
                                                        } else if ($order_in_list->status === 'completed') {
                                                            echo 'Hoàn thành';
                                                        } else if ($order_in_list->status === 'declined') {
                                                            echo 'Đơn hủy';
                                                        }
                                                    @endphp
                                                </p>
                                                <p><strong>Phương thức thanh toán:</strong>
                                                    @php
                                                        if ($order_in_list->payment_method === 'cod') {
                                                            echo ' Thanh toán khi nhận hàng (COD)';
                                                        } else if ($order_in_list->payment_method === 'bank') {
                                                            echo 'Chuyển khoản ngân hàng';
                                                        }
                                                    @endphp
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal footer -->
                <div
                    class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="info_user_order" type="button"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDetails(id) {
            var detailsRow = document.getElementById('details-' + id);
            if (detailsRow.classList.contains('hidden')) {
                detailsRow.classList.remove('hidden');
            } else {
                detailsRow.classList.add('hidden');
            }
        }
    </script>


</div>
