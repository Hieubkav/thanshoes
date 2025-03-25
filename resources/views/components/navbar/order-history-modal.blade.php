<!-- Modal đơn đã đặt -->
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
                                        {{ $order_in_list->items->sum('quantity')   }} món
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ number_format($order_in_list->total_price) }}đ
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
                                                @foreach( $order_in_list->items as $j => $order_item )
                                                    <li>Sản phẩm: {{ optional($order_item->variant)->color ?? 'N/A' }}/{{ optional($order_item->variant)->size ?? 'N/A' }} 
                                                        - Số lượng: {{ $order_item->quantity }} - Giá 1 sản phẩm: {{ number_format($order_item->price) }} đ - Tổng giá: {{ number_format($order_item->price*$order_item->quantity) }} đ</li>
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
            <div class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
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