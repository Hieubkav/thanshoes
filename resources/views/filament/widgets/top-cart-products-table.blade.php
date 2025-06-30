<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Top 20 sản phẩm được thêm vào giỏ hàng nhiều nhất
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Sản phẩm</th>
                        <th class="px-4 py-3">Thương hiệu</th>
                        <th class="px-4 py-3">Size</th>
                        <th class="px-4 py-3">Màu</th>
                        <th class="px-4 py-3">Số lần thêm</th>
                        <th class="px-4 py-3">Tổng số lượng</th>
                        <th class="px-4 py-3">Lần cuối thêm</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $index => $product)
                        <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-4 py-3 font-medium">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900 dark:text-white leading-tight">
                                    {{ $product->product_name }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($product->brand)
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                        {{ $product->brand }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                    {{ $product->size }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">
                                    {{ $product->color }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                    {{ $product->total_added }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">
                                    {{ $product->total_quantity }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($product->last_added)->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                Chưa có dữ liệu giỏ hàng
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
