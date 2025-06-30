<div wire:poll.7s="loadRecentActivity" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse mr-2"></div>
            🔔 Hoạt động gần đây
        </h3>
        <button
            wire:click="toggleNotifications"
            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
        >
            {{ $showNotifications ? '🔕 Ẩn' : '🔔 Hiện' }}
        </button>
    </div>

    @if($showNotifications)
    <div class="p-4">
        <!-- Recent Website Visits -->
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                🌐 Lượt truy cập website (10 phút gần nhất)
            </h4>
            @if(count($recentVisits) > 0)
                <div class="space-y-2">
                    @foreach($recentVisits as $visit)
                    <div class="flex items-center justify-between p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-sm">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $visit['ip'] }}</span>
                            <span class="text-gray-600 dark:text-gray-400">→ {{ $visit['page'] }}</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $visit['time'] }}
                            @if($visit['referrer'] !== 'Direct')
                                <span class="ml-1 text-blue-600 dark:text-blue-400">từ {{ $visit['referrer'] }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 italic">Chưa có hoạt động nào trong 10 phút gần nhất</p>
            @endif
        </div>

        <!-- Recent Product Views -->
        <div>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                👟 Lượt xem sản phẩm (10 phút gần nhất)
            </h4>
            @if(count($recentProductViews) > 0)
                <div class="space-y-2">
                    @foreach($recentProductViews as $view)
                    <div class="flex items-center justify-between p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg text-sm">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $view['ip'] }}</span>
                            <span class="text-gray-600 dark:text-gray-400">xem</span>
                            <span class="font-medium text-purple-700 dark:text-purple-400">{{ Str::limit($view['product'], 30) }}</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $view['time'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 italic">Chưa có lượt xem sản phẩm nào trong 10 phút gần nhất</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="px-4 py-2 bg-gray-50 dark:bg-gray-700 rounded-b-lg">
        <p class="text-xs text-gray-400 dark:text-gray-500 text-center">
            🔄 Tự động cập nhật mỗi 7 giây | 🔒 IP được ẩn một phần để bảo mật
        </p>
    </div>
</div>
