<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Phân tích chi tiết lưu lượng
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Pages -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">📄 Top Pages (7 ngày)</h3>
                <div class="space-y-3">
                    @forelse($topPages as $page)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white text-sm leading-tight">
                                        {{ $page->clean_url }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $page->unique_visitors }} visitors</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $page->total_views }}</div>
                                <div class="text-xs text-gray-500">views</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center">Chưa có dữ liệu</p>
                    @endforelse
                </div>
            </div>

            <!-- Top Referrers -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">🔗 Traffic Sources</h3>
                <div class="space-y-3">
                    @forelse($topReferrers as $referrer)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                <span class="font-medium text-gray-900 dark:text-white text-sm leading-tight">
                                    {{ $referrer->clean_referrer }}
                                </span>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $referrer->visits }}</div>
                                <div class="text-xs text-gray-500">visits</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center">Chưa có dữ liệu</p>
                    @endforelse
                </div>
            </div>

            <!-- Device Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">📱 Thiết bị</h3>
                <div class="space-y-3">
                    @forelse($deviceStats as $device => $count)
                        @php
                            $total = $deviceStats->sum();
                            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                            $color = match($device) {
                                'Mobile' => 'bg-green-500',
                                'Desktop' => 'bg-blue-500',
                                'Tablet' => 'bg-purple-500',
                                default => 'bg-gray-500'
                            };
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 {{ $color }} rounded-full"></div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $device }}</span>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $percentage }}%</div>
                                <div class="text-xs text-gray-500">{{ $count }} visits</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center">Chưa có dữ liệu</p>
                    @endforelse
                </div>
            </div>

            <!-- Peak Hours -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">⏰ Giờ cao điểm</h3>
                <div class="space-y-3">
                    @forelse($peakHours as $hour)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $hour->hour }}:00 - {{ $hour->hour + 1 }}:00</span>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $hour->visits }}</div>
                                <div class="text-xs text-gray-500">visits</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center">Chưa có dữ liệu</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Weekly Trend -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg p-4 border dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">📊 Xu hướng 7 ngày</h3>
            <div class="grid grid-cols-7 gap-4">
                @foreach($weeklyData as $day)
                    <div class="text-center">
                        <div class="h-20 bg-indigo-200 dark:bg-indigo-800 rounded-lg mb-2 flex flex-col items-center justify-end p-2" 
                             style="height: {{ $day['visitors'] > 0 ? max(20, ($day['visitors'] / max(array_column($weeklyData, 'visitors'))) * 80) : 20 }}px">
                            @if($day['visitors'] > 0)
                                <span class="text-xs font-semibold text-indigo-800 dark:text-indigo-200">{{ $day['visitors'] }}</span>
                            @endif
                        </div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $day['date'] }}</div>
                        <div class="text-xs text-gray-500">{{ $day['page_views'] }} views</div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
