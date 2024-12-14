<div class="max-w-screen-xl mx-auto px-4 py-6">
    <!-- Header Section with Countdown -->
    <div class="flex justify-between items-center mb-4 border-b pb-2">
        <div class="flex items-center space-x-2">
            <h2 class="text-lg font-bold text-gray-700">GIẢM SỐC 50%</h2>
            <span class="text-lg">🔥</span>
        </div>
        <div class="flex space-x-2 text-sm font-semibold text-gray-600">
            <span>Hàng bán chạy</span>
            <span>|</span>
            <span>Miễn phí vận chuyển</span>
            <span>|</span>
            <span>Chính sách đổi trả</span>
        </div>
        <div class="flex items-center space-x-1 text-white bg-gray-800 rounded px-2 py-1 text-xs font-semibold">
            <span>Hết khuyến mãi trong</span>
            <span class="px-1 bg-red-500 rounded">08</span>:
            <span class="px-1 bg-red-500 rounded">51</span>:
            <span class="px-1 bg-red-500 rounded">39</span>
        </div>
    </div>

    <!-- Product Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mt-4">
        <!-- Product Card 1 -->
        <div class="border rounded-lg p-3 bg-white shadow hover:shadow-lg transition">
            <div class="relative group">
                <img src="https://bizweb.dktcdn.net/100/484/026/products/frame-4491.jpg?v=1685581775777" alt="Product 1"
                    class="rounded-lg w-full h-56 object-cover">
                <!-- Promotion Badge -->
                <span class="absolute top-2 left-2 bg-purple-500 text-white text-xs font-semibold px-2 py-1 rounded">MUA
                    2 TẶNG 1</span>
            </div>
            <!-- Product Info -->
            <div class="mt-4">
                <h3 class="text-sm font-semibold text-gray-800">Áo croptop tập gym yoga</h3>
                <div class="flex items-center mt-2">
                    <p class="text-red-500 font-semibold">290.000₫</p>
                    <p class="text-gray-500 line-through text-sm ml-2">350.000₫</p>
                    <span class="text-xs text-red-500 font-semibold ml-1">-18%</span>
                </div>
            </div>
            <!-- Color Options -->
            <div class="flex items-center mt-3 space-x-2">
                <img src="https://via.placeholder.com/40" alt="Color option 1"
                    class="w-8 h-8 rounded-full border border-gray-300">
                <img src="https://via.placeholder.com/40" alt="Color option 2"
                    class="w-8 h-8 rounded-full border border-gray-300">
                <span class="text-gray-500 text-sm">+7</span>
            </div>
        </div>

        <!-- Repeat Product Cards -->
        <!-- Product Card 2 -->
        @for ($i = 0; $i < 7; $i++)
            <div class="border rounded-lg p-3 bg-white shadow hover:shadow-lg transition">
                <div class="relative group">
                    <img src="https://bizweb.dktcdn.net/100/484/026/products/frame-4491.jpg?v=1685581775777"
                        alt="Product 2" class="rounded-lg w-full h-56 object-cover">
                    <!-- Promotion Badge -->
                    <span
                        class="absolute top-2 left-2 bg-blue-600 text-white text-xs font-semibold px-2 py-1 rounded">FREESHIP</span>
                </div>
                <!-- Product Info -->
                <div class="mt-4">
                    <h3 class="text-sm font-semibold text-gray-800">Quần legging lửng tập yoga</h3>
                    <div class="flex items-center mt-2">
                        <p class="text-red-500 font-semibold">375.000₫</p>
                        <p class="text-gray-500 line-through text-sm ml-2">400.000₫</p>
                        <span class="text-xs text-red-500 font-semibold ml-1">-7%</span>
                    </div>
                </div>
                <!-- Color Options -->
                <div class="flex items-center mt-3 space-x-2">
                    <img src="https://via.placeholder.com/40" alt="Color option 1"
                        class="w-8 h-8 rounded-full border border-gray-300">
                    <img src="https://via.placeholder.com/40" alt="Color option 2"
                        class="w-8 h-8 rounded-full border border-gray-300">
                    <span class="text-gray-500 text-sm">+2</span>
                </div>
            </div>
        @endfor


        <!-- Add more product cards as needed -->

    </div>

    <!-- View All Button -->
    <div class="flex justify-center mt-6">
        <button class="px-6 py-2 border rounded-full text-gray-700 hover:bg-gray-100 transition">Xem tất cả</button>
    </div>
</div>
