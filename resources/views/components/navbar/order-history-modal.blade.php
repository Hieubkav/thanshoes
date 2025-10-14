<!-- Modal đơn đã đặt -->
<div id="info_user_order" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full"
    x-cloak data-cloak>
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    @auth('customers')
                        Đơn hàng của tôi
                    @else
                        Lịch sử đơn hàng
                    @endauth
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="info_user_order">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5 space-y-4 text-gray-700 dark:text-gray-300">
                @auth('customers')
                    <!-- Hiển thị đơn hàng cho user đã đăng nhập -->
                    @if(($pendingOrdersCount ?? 0) > 0)
                        <div class="rounded-lg border border-amber-200 bg-amber-50 text-amber-800 px-4 py-3 text-sm flex items-start gap-2">
                            <i class="fas fa-hourglass-half mt-0.5"></i>
                            <div>
                                <p class="font-semibold">Bạn có {{ $pendingOrdersCount }} đơn hàng chưa hoàn thành</p>
                                <p>Theo dõi thường xuyên để cập nhật trạng thái và hỗ trợ xử lý nhanh chóng.</p>
                            </div>
                        </div>
                    @endif
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
                                @if (isset($order) && $order->count() > 0)
                                @foreach ($order as $i => $order_in_list)
                                    <tr>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $i + 1 }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $order_in_list->items->sum('quantity') }} món
                                        </td>                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            @if ($order_in_list->discount_amount > 0)
                                                <span class="line-through text-gray-400">{{ number_format($order_in_list->original_total) }}đ</span><br>
                                                <span class="text-red-600">-{{ number_format($order_in_list->discount_amount) }}đ</span><br>
                                                <span class="font-semibold text-green-600">{{ number_format($order_in_list->total) }}đ</span>
                                            @else
                                                {{ number_format($order_in_list->total_price) }}đ
                                            @endif
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{ $order_in_list->created_at->format('d/m/Y') }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
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
                                                    @foreach ($order_in_list->items as $order_item)
                                                        <li class="flex items-center gap-4 mb-4">
                                                            @if($order_item->variant && $order_item->variant->variantImage)
                                                                <img src="{{ $order_item->variant->variantImage->image }}"
                                                                     alt="Hình ảnh sản phẩm"
                                                                     class="w-16 h-16 object-cover rounded-lg">
                                                            @else
                                                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                                                    <span class="text-gray-400">Không có ảnh</span>
                                                                </div>
                                                            @endif
                                                            <div class="flex-1">
                                                                @if ($order_item->variant && $order_item->variant->product)
                                                                    <h4 class="font-medium text-base mb-1">{{ $order_item->variant->product->name }}</h4>
                                                                    <p class="text-gray-600 dark:text-gray-400 mb-1">
                                                                        Màu: {{ $order_item->variant->color }} -
                                                                        Size: {{ $order_item->variant->size }}
                                                                    </p>
                                                                @else
                                                                    <h4 class="font-medium text-base mb-1">Sản phẩm không còn tồn tại</h4>
                                                                    <p class="text-gray-600 dark:text-gray-400 mb-1">
                                                                        Thông tin không khả dụng
                                                                    </p>
                                                                @endif
                                                                <p class="text-gray-600 dark:text-gray-400">
                                                                    Số lượng: {{ $order_item->quantity }} -
                                                                    Giá: {{ number_format($order_item->price) }}đ
                                                                </p>                                                <p class="font-medium text-blue-600 dark:text-blue-400">
                                                                    Tổng: {{ number_format($order_item->price * $order_item->quantity) }}đ
                                                                </p>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                
                                                @if ($order_in_list->discount_amount > 0)
                                                <div class="mt-4 mb-3 p-3 bg-gray-100 dark:bg-gray-600 rounded-lg">
                                                    <h4 class="font-semibold text-base mb-2">Thông tin giảm giá</h4>
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <p class="text-gray-600 dark:text-gray-400">Tổng tiền gốc:</p>
                                                            <p class="font-medium line-through">{{ number_format($order_in_list->original_total) }}đ</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-600 dark:text-gray-400">Giảm giá:</p>
                                                            <p class="font-medium text-red-600">
                                                                -{{ number_format($order_in_list->discount_amount) }}đ
                                                                @if($order_in_list->discount_type == 'percent')
                                                                    ({{ number_format($order_in_list->discount_percentage, 2) }}%)
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <div class="col-span-2">
                                                            <p class="text-gray-600 dark:text-gray-400">Thành tiền:</p>
                                                            <p class="font-semibold text-green-600">{{ number_format($order_in_list->total) }}đ</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                <p><strong>Trạng thái:</strong>
                                                    @php
                                                        if ($order_in_list->status === 'pending') {
                                                            echo 'Đang chờ xử lý';
                                                        } elseif ($order_in_list->status === 'processing') {
                                                            echo 'Đã xử lý';
                                                        } elseif ($order_in_list->status === 'completed') {
                                                            echo 'Hoàn thành';
                                                        } elseif ($order_in_list->status === 'declined') {
                                                            echo 'Đơn hủy';
                                                        }
                                                    @endphp
                                                </p>
                                                <p><strong>Phương thức thanh toán:</strong>
                                                    @php
                                                        if ($order_in_list->payment_method === 'cod') {
                                                            echo ' Thanh toán khi nhận hàng (COD)';
                                                        } elseif ($order_in_list->payment_method === 'bank') {
                                                            echo 'Chuyển khoản ngân hàng';
                                                        }
                                                    @endphp
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-shopping-bag text-4xl text-gray-400 mb-4"></i>
                                                <p class="text-gray-500 dark:text-gray-400">Bạn chưa có đơn hàng nào</p>
                                                <a href="{{ route('shop.store_front') }}" class="mt-2 text-blue-600 hover:text-blue-800">
                                                    Bắt đầu mua sắm
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Thông báo cho khách chưa đăng nhập -->
                    <div class="text-center py-8">
                        <i class="fas fa-user-lock text-4xl text-gray-400 mb-4"></i>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            Vui lòng đăng nhập
                        </h4>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">
                            Bạn cần đăng nhập để xem lịch sử đơn hàng
                        </p>
                        <div class="space-x-4">
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Đăng nhập
                            </a>
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-user-plus mr-2"></i>
                                Đăng ký
                            </a>
                        </div>
                    </div>
                @endauth
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
