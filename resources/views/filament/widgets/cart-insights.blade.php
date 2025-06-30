<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Phân tích chi tiết giỏ hàng
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Thương hiệu -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Top Thương hiệu</h3>
                <div class="space-y-3">
                    @forelse($topBrands as $brand)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span class="font-medium text-gray-900 dark:text-white leading-tight">
                                    {{ $brand->brand }}
                                </span>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $brand->total_added }} lần</div>
                                <div class="text-xs text-gray-500">{{ $brand->total_quantity }} sản phẩm</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center">Chưa có dữ liệu</p>
                    @endforelse
                </div>
            </div>

            <!-- Top Size -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Size phổ biến</h3>
                <div class="space-y-2">
                    @php
                        $maxAdded = $topSizes->max('total_added');
                    @endphp
                    @foreach($topSizes as $size)
                        <div class="flex items-center space-x-3">
                            <div class="text-sm font-bold text-gray-900 dark:text-white w-8 text-center">{{ $size->size }}</div>
                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-4 relative overflow-hidden">
                                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-full rounded-full transition-all duration-300"
                                     style="width: {{ $maxAdded > 0 ? ($size->total_added / $maxAdded) * 100 : 0 }}%"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-xs font-semibold text-white drop-shadow">{{ $size->total_added }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Thông tin bổ sung -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ $highValueCarts }}</div>
                <div class="text-sm opacity-90">Giỏ hàng > 1 triệu</div>
                <div class="text-xs opacity-75 mt-1">Khách hàng tiềm năng cao</div>
            </div>
            
            <div class="bg-gradient-to-r from-blue-500 to-cyan-500 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ $topBrands->count() }}</div>
                <div class="text-sm opacity-90">Thương hiệu</div>
                <div class="text-xs opacity-75 mt-1">Đa dạng brand</div>
            </div>
            
            <div class="bg-gradient-to-r from-green-500 to-teal-500 rounded-lg p-4 text-white">
                <div class="text-2xl font-bold">{{ $topSizes->count() }}</div>
                <div class="text-sm opacity-90">Size đa dạng</div>
                <div class="text-xs opacity-75 mt-1">Lựa chọn phong phú</div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
