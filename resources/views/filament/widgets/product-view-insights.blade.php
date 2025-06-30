<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Phân tích lượt xem sản phẩm
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top sản phẩm hôm nay -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">🔥 Hot hôm nay</h3>
                <div class="space-y-3">
                    @forelse($topProductsToday as $index => $product)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs font-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white text-sm leading-tight">
                                        {{ $product->name }}
                                    </div>
                                    @if($product->brand)
                                        <div class="text-xs text-gray-500">{{ $product->brand }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $product->total_views }}</div>
                                <div class="text-xs text-gray-500">{{ $product->unique_viewers }} người</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center">Chưa có lượt xem hôm nay</p>
                    @endforelse
                </div>
            </div>

            <!-- Top sản phẩm all time -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">👑 Best sellers</h3>
                <div class="space-y-3">
                    @forelse($topProductsAllTime as $index => $product)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 bg-yellow-500 text-white rounded-full flex items-center justify-center text-xs font-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white text-sm leading-tight">
                                        {{ $product->name }}
                                    </div>
                                    @if($product->brand)
                                        <div class="text-xs text-gray-500">{{ $product->brand }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($product->total_views) }}</div>
                                <div class="text-xs text-gray-500">{{ $product->unique_viewers }} người</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center">Chưa có dữ liệu</p>
                    @endforelse
                </div>
            </div>

            <!-- Trending products -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">📈 Trending hôm nay</h3>
                <div class="space-y-3">
                    @forelse($trendingProducts as $product)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white text-sm leading-tight">
                                        {{ $product->name }}
                                    </div>
                                    @if($product->brand)
                                        <div class="text-xs text-gray-500">{{ $product->brand }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-green-600">{{ $product->today_views }}</div>
                                <div class="text-xs text-gray-500">{{ $product->unique_viewers }} người</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center">Chưa có sản phẩm trending</p>
                    @endforelse
                </div>
            </div>

            <!-- Brand performance -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">🏷️ Thương hiệu (7 ngày)</h3>
                <div class="space-y-3">
                    @forelse($brandViews as $brand)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $brand->brand }}</span>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($brand->total_views) }}</div>
                                <div class="text-xs text-gray-500">{{ $brand->unique_viewers }} người</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center">Chưa có dữ liệu</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Hoạt động theo giờ -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg p-4 border dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">⏰ Lượt xem theo giờ (hôm nay)</h3>
            <div class="space-y-2">
                @php
                    $maxViews = max($hourlyData);
                    $chunks = array_chunk($hourlyData, 6, true); // Chia thành 4 hàng, mỗi hàng 6 giờ
                @endphp
                @foreach($chunks as $chunk)
                    <div class="grid grid-cols-6 gap-3">
                        @foreach($chunk as $hour => $views)
                            <div class="flex items-center space-x-2">
                                <div class="text-xs font-medium text-gray-600 dark:text-gray-400 w-8">{{ $hour }}h</div>
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3 relative overflow-hidden">
                                    @if($views > 0)
                                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-full rounded-full transition-all duration-300"
                                             style="width: {{ $maxViews > 0 ? ($views / $maxViews) * 100 : 0 }}%"></div>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="text-xs font-semibold text-white drop-shadow">{{ $views }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Thống kê conversion -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ $viewToCartRate }}%</div>
                <div class="text-sm opacity-90">View → Cart Rate</div>
                <div class="text-xs opacity-75 mt-1">Tỷ lệ chuyển đổi (7 ngày)</div>
            </div>
            
            <div class="bg-gradient-to-r from-pink-500 to-rose-500 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ $topProductsToday->count() }}</div>
                <div class="text-sm opacity-90">Sản phẩm có view hôm nay</div>
                <div class="text-xs opacity-75 mt-1">Đa dạng sản phẩm</div>
            </div>
            
            <div class="bg-gradient-to-r from-cyan-500 to-blue-500 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ $brandViews->count() }}</div>
                <div class="text-sm opacity-90">Thương hiệu có view</div>
                <div class="text-xs opacity-75 mt-1">Phong phú brand</div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
