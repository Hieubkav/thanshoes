<!-- Modal thông tin user -->
<div id="info_user_modal" tabindex="-1" aria-hidden="true"
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-screen bg-black bg-opacity-50">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-xl dark:bg-gray-800 transform transition-all duration-300 ease-in-out">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    @auth('customers')
                        Thông tin tài khoản
                    @else
                        Thông tin khách hàng
                    @endauth
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
                @auth('customers')
                    @if(($pendingOrdersCount ?? 0) > 0)
                        <div class="rounded-lg border border-amber-200 bg-amber-50 text-amber-800 px-4 py-3 text-sm flex items-start gap-2">
                            <i class="fas fa-exclamation-triangle mt-0.5"></i>
                            <div>
                                <p class="font-semibold">Đơn hàng đang xử lý</p>
                                <p>Bạn còn {{ $pendingOrdersCount }} đơn hàng chưa hoàn thành. Theo dõi để nhận cập nhật giao hàng nhé.</p>
                                <a href="{{ route('customer.orders.index') }}" class="mt-2 inline-flex items-center gap-1 text-amber-900 font-semibold hover:text-amber-700">
                                    Xem tất cả đơn
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                    <!-- Thông tin từ Customer đã đăng nhập -->
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-user-circle text-3xl text-blue-600"></i>
                        <div>
                            <p class="font-semibold">Tên khách hàng</p>
                            <p>{{ auth('customers')->user()->name }}</p>
                        </div>
                    </div>
                    @if(auth('customers')->user()->email)
                        <div class="flex items-center space-x-4">
                            <i class="fas fa-envelope text-3xl text-red-600"></i>
                            <div>
                                <p class="font-semibold">Email</p>
                                <p>{{ auth('customers')->user()->email }}</p>
                            </div>
                        </div>
                    @endif
                    @if(auth('customers')->user()->phone)
                        <div class="flex items-center space-x-4">
                            <i class="fas fa-phone-alt text-3xl text-green-600"></i>
                            <div>
                                <p class="font-semibold">Số điện thoại</p>
                                <p>{{ auth('customers')->user()->phone }}</p>
                            </div>
                        </div>
                    @endif
                    @if(auth('customers')->user()->address)
                        <div class="flex items-center space-x-4">
                            <i class="fas fa-map-marker-alt text-3xl text-yellow-600"></i>
                            <div>
                                <p class="font-semibold">Địa chỉ</p>
                                <p>{{ auth('customers')->user()->address }}</p>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Thông tin khách hàng chưa đăng nhập -->
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-user-circle text-3xl text-blue-600"></i>
                        <div>
                            <p class="font-semibold">Tên khách hàng</p>
                            <p>{{ $name_customer ?? 'Chưa có thông tin' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-phone-alt text-3xl text-green-600"></i>
                        <div>
                            <p class="font-semibold">Số điện thoại</p>
                            <p>{{ $phone_customer ?? 'Chưa có thông tin' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-envelope text-3xl text-red-600"></i>
                        <div>
                            <p class="font-semibold">Email</p>
                            <p>{{ $email_customer ?? 'Chưa có thông tin' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-map-marker-alt text-3xl text-yellow-600"></i>
                        <div>
                            <p class="font-semibold">Địa chỉ</p>
                            <p>{{ $address_customer ?? 'Chưa có thông tin' }}</p>
                        </div>
                    </div>
                @endauth
            </div>
            <!-- Modal footer -->
            <div class="flex items-center justify-between p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                <div class="flex items-center">
                    <label for="save-info" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Bạn đặt hàng thông tin sẽ tự động được lưu</label>
                </div>
                <button data-modal-hide="info_user_modal" type="button"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Đóng
                </button>
            </div>
        </div>
    </div>
</div>
