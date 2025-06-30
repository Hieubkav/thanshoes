<div wire:poll.5s="updateStats" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">📊 Live Statistics</h3>
        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2"></div>
            <span>Cập nhật: {{ $lastUpdate }}</span>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Visitor hôm nay -->
        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($todayVisitors) }}</div>
            <div class="text-sm text-blue-800 dark:text-blue-300">Visitor hôm nay</div>
        </div>

        <!-- Page views hôm nay -->
        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($todayPageViews) }}</div>
            <div class="text-sm text-green-800 dark:text-green-300">Lượt xem trang</div>
        </div>

        <!-- Product views hôm nay -->
        <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($todayProductViews) }}</div>
            <div class="text-sm text-purple-800 dark:text-purple-300">Lượt xem sản phẩm</div>
        </div>

        <!-- Online visitors -->
        <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($onlineVisitors) }}</div>
            <div class="text-sm text-orange-800 dark:text-orange-300">Đang online</div>
        </div>
    </div>

    <div class="mt-4 text-xs text-gray-400 dark:text-gray-500 text-center">
        🔄 Tự động cập nhật mỗi 5 giây | 🟢 Online = hoạt động trong 5 phút gần nhất
    </div>
</div>
