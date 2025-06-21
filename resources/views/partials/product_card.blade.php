{{-- Modern Product Card Component --}}
<div class="group bg-white rounded-xl shadow-soft hover:shadow-soft-lg transition-all duration-300 overflow-hidden border border-neutral-200/50 hover:border-primary-200">
    <!-- Product Image -->
    <div class="relative overflow-hidden bg-neutral-50">
        <img src="https://bizweb.dktcdn.net/100/484/026/themes/953543/assets/frame_2.png?1726470850088"
             alt="Quần legging lửng tập yoga"
             loading="lazy"
             class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105">

        <!-- Discount Badge -->
        <div class="absolute top-3 left-3">
            <span class="chip chip-error text-xs font-semibold">
                -7%
            </span>
        </div>

        <!-- Hover Actions -->
        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center">
            <div class="flex space-x-3 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                <button class="btn btn-secondary btn-sm rounded-full w-10 h-10 p-0 shadow-lg hover:shadow-xl">
                    <i class="fas fa-sliders-h text-sm"></i>
                </button>
                <button class="btn btn-primary btn-sm rounded-full w-10 h-10 p-0 shadow-lg hover:shadow-xl">
                    <i class="fas fa-eye text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Product Details -->
    <div class="p-5">
        <!-- Brand -->
        <div class="mb-2">
            <span class="text-neutral-500 text-xs font-medium uppercase tracking-wider">EGA</span>
        </div>

        <!-- Product Name -->
        <h3 class="text-lg font-semibold text-neutral-900 mb-3 line-clamp-2 group-hover:text-primary-600 transition-colors duration-200">
            Quần legging lửng tập yoga
        </h3>

        <!-- Price Section -->
        <div class="flex items-center space-x-2 mb-4">
            <span class="text-xl font-bold text-primary-600">375.000₫</span>
            <span class="text-sm text-neutral-500 line-through">400.000₫</span>
        </div>

        <!-- Color Variants -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="flex -space-x-1">
                    <div class="w-6 h-6 rounded-full border-2 border-white shadow-sm bg-black"></div>
                    <div class="w-6 h-6 rounded-full border-2 border-white shadow-sm bg-amber-100"></div>
                </div>
                <span class="text-xs text-neutral-500 font-medium">+2 màu</span>
            </div>

            <!-- Quick Add Button -->
            <button class="btn btn-primary btn-sm opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0">
                <i class="fas fa-plus text-xs mr-1"></i>
                Thêm
            </button>
        </div>
    </div>
</div>
