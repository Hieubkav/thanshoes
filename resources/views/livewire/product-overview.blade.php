<div>
    @php
        use App\Helpers\PriceHelper;
        use App\Models\Setting;
        $settings = Setting::first();
        $discountPercent = PriceHelper::getDiscountPercentage();
        $discountType = PriceHelper::getDiscountType();
    @endphp
    <div class="bg-gray-50 dark:bg-gray-800 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap -mx-4">
                <!-- Phần hình ảnh sản phẩm -->
                <div class="w-full md:w-1/2 px-4 mb-8">
                    <div class="relative">
                        <img id="mainImage" src="{{ $main_image }}" alt="Product Image"
                            class="w-full h-auto rounded-lg shadow-md mb-4 object-cover">
                        @if ($product->variants->min('price') >= 500000)
                            <div class="absolute top-0 left-0 bg-blue-500 text-white px-2 py-1 rounded-br-lg">
                                Freeship
                            </div>
                        @endif
                        @if($discountPercent > 0)
                        <div class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 rounded-bl-lg">
                            @if($discountType === 'percentage')
                                -{{ $discountPercent }}%
                            @else
                                -{{ number_format($discountPercent, 0, ',', '.') }}vnd
                            @endif
                        </div>
                        @endif
                    </div>
                    <div class="flex gap-4 py-4 overflow-x-auto">
                        @foreach ($list_images_product as $item)
                            @if ($item->type == 'variant')
                                <img src="{{ $item->image }}"
                                    class="w-20 h-20 object-cover rounded-md hover:opacity-90 transition duration-300"
                                    onclick="changeImage(this.src)">
                            @else
                                <img src="{{ asset('storage/' . $item->image) }}"
                                    class="w-20 h-20 object-cover rounded-md hover:opacity-90 transition duration-300"
                                    onclick="changeImage(this.src)">
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Phần chi tiết sản phẩm -->
                <div class="w-full md:w-1/2 px-4">
                    <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">
                        {{ $product->name }}
                    </h2>
                    @if ($product->brand || $product->type)
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            @if ($product->brand)
                                {{ $product->brand }}
                            @endif
                            @if ($product->type)
                                {{ $product->type }}
                            @endif
                        </p>
                    @endif
                    <div class="mb-4">
                        <span class="text-2xl font-bold text-gray-800 dark:text-white">
                            @if ($product->variants->min('price') == $product->variants->max('price'))
                                @php
                                    $price = $product->variants->min('price');
                                    $discountedPrice = PriceHelper::calculateDiscountedPrice($price);
                                @endphp
                                {{ number_format($discountedPrice, 0, ',', '.') }}vnd
                            @else
                                @php
                                    $minPrice = $product->variants->min('price');
                                    $maxPrice = $product->variants->max('price');
                                    $discountedMinPrice = PriceHelper::calculateDiscountedPrice($minPrice);
                                    $discountedMaxPrice = PriceHelper::calculateDiscountedPrice($maxPrice);
                                @endphp
                                {{ number_format($discountedMinPrice, 0, ',', '.') }}vnd
                                -
                                {{ number_format($discountedMaxPrice, 0, ',', '.') }}vnd
                            @endif
                        </span>
                        @if($discountPercent > 0)
                        <span class="text-gray-500 line-through italic">
                            @php
                                $displayOriginalPrice = PriceHelper::getDisplayOriginalPrice($product->variants->max('price'));
                            @endphp
                            {{ number_format($displayOriginalPrice, 0, ',', '.') }}vnd
                        </span>
                        @endif
                    </div>

                    <!-- Chọn màu sắc -->
                    @if (count($list_colors) > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-800 dark:text-white mb-4">Chọn phân loại ngay:</h3>
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3">
                                @foreach ($list_colors as $color)
                                    @php
                                        $link_src_color_pic =
                                            optional(
                                                $product->variants
                                                    ->where('color', $color)
                                                    ->filter(fn($variant) => $variant->variantImage)
                                                    ->random()?->variantImage,
                                            )->image ?? asset('images/logo.svg');
                                        $isDisabled = $product->variants->where('color', $color)->sum('stock') == 0;
                                        $isSelected = $countfilter == 2 && $color == $selectedColor;
                                    @endphp
                                    <label
                                        class="group relative flex flex-col items-center justify-center p-3 border rounded-lg transition-all duration-300 {{ $isDisabled
                                            ? 'opacity-50 pointer-events-none'
                                            : ($isSelected
                                                ? 'bg-white ring-2 ring-blue-500 text-blue-600 font-semibold'
                                                : 'bg-white hover:bg-gray-50') }}">
                                        <input wire:model.live.debounce.300ms="selectedColor"
                                            @if ($isDisabled) disabled @endif
                                            value="{{ $color }}" type="radio" name="color" class="hidden">
                                        <div
                                            class="w-12 h-12 flex items-center justify-center rounded-full overflow-hidden bg-gray-50">
                                            <img src="{{ $link_src_color_pic }}" alt="{{ $color }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <span
                                            class="mt-2 text-xs sm:text-sm break-words text-center max-w-full">{{ $color }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Chọn kích cỡ -->
                    @if (count($list_sizes) > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-green-500 italic dark:text-white mb-4">Chọn phân loại
                                ngay:</h3>
                            <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                                @foreach ($list_sizes as $size)
                                    @php
                                        $isDisabled =
                                            $countfilter == 1
                                                ? $product->variants->where('size', $size)->sum('stock') == 0
                                                : $product->variants
                                                        ->where('size', $size)
                                                        ->where('color', $selectedColor)
                                                        ->sum('stock') == 0;
                                        $isSelected = $size == $selectedSize;
                                    @endphp
                                    <label
                                        class="relative flex items-center justify-center p-2 border rounded-lg transition-all duration-300 {{ $isDisabled
                                            ? 'opacity-50 pointer-events-none'
                                            : ($isSelected
                                                ? 'bg-white ring-2 ring-green-500 text-green-600 font-semibold'
                                                : 'bg-white hover:bg-gray-50') }}">
                                        <input wire:model.live.debounce.300ms="selectedSize"
                                            @if ($isDisabled) disabled @endif
                                            value="{{ $size }}" type="radio" name="size" class="hidden">
                                        <span class="text-sm sm:text-base break-words">{{ $size }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Số lượng tồn kho -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Mua nhanh chỉ còn:</h3>
                        <span class="text-red-500 font-bold italic">
                            @if ($countfilter == 2)
                                {{ $product->variants->where('color', $selectedColor)->where('size', $selectedSize)->sum('stock') }}
                            @else
                                {{ $product->variants->where('size', $selectedSize)->sum('stock') }}
                            @endif
                            sản phẩm
                        </span>
                    </div>

                    <!-- Nút Thêm vào giỏ hàng và Bảng size -->
                    <div class="flex items-start space-x-4 mb-6" x-data="{ showSizeModal: false }">
                        <div>
                            @include('partials.button_add_cart')
                        </div>
                        @if(\App\Models\Setting::first()->size_shoes_image)
                            <div class="relative">
                                <button type="button"
                                    @click="showSizeModal = true"
                                    class="group relative rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                                    <img src="{{ asset('storage/' . \App\Models\Setting::first()->size_shoes_image) }}"
                                        alt="Bảng size giày"
                                        class="w-auto h-[200px] object-cover rounded-lg group-hover:opacity-90 transition-opacity">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300 flex items-center justify-center">
                                        <span class="text-white opacity-0 group-hover:opacity-100 transition-opacity text-sm font-medium">
                                            Xem chi tiết
                                        </span>
                                    </div>
                                </button>

                                <!-- Modal Bảng Size -->
                                <div x-show="showSizeModal"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform scale-90"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 transform scale-100"
                                    x-transition:leave-end="opacity-0 transform scale-90"
                                    class="fixed inset-0 z-50 overflow-y-auto"
                                    style="display: none;">
                                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                                            @click="showSizeModal = false"></div>

                                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                                            <div class="bg-white p-6">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="w-full">
                                                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                                                            Bảng Size Giày
                                                        </h3>
                                                        <img src="{{ asset('storage/' . \App\Models\Setting::first()->size_shoes_image) }}"
                                                            alt="Bảng size giày"
                                                            class="w-full h-auto rounded-lg">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <button type="button"
                                                    @click="showSizeModal = false"
                                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                    Đóng
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        <!-- Mô tả sản phẩm -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Mô tả sản phẩm</h3>
            <div class="prose max-w-none text-gray-600 dark:text-gray-300">
                {!! $product->description !!}
            </div>
        </div>
    </div>

    <script>
        function changeImage(src) {
            document.getElementById('mainImage').src = src;
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('colorSelected', (data) => {
                console.log(data[0]);
            });

            Livewire.on('sizeSelected', (data) => {
                console.log(data[0]);
            });

            Livewire.on('checkcolorfirst', () => {
                alert('Vui lòng chọn phân mục ở trên trước');
            });
        });
    </script>


    <!-- Keep everything above the related products section the same -->

    <!-- Sản phẩm cùng danh mục -->
    @if ($related_products->isNotEmpty())
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">Sản phẩm cùng danh mục</h3>
                <a href="/catfilter?type={{ urlencode($product->type) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition duration-200">
                    Xem tất cả
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
            @if ($related_products->count() <= 4)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($related_products as $related)
                        @php
                            $relatedMinPrice = $related->variants->min('price');
                            $relatedDiscountedPrice = PriceHelper::calculateDiscountedPrice($relatedMinPrice);
                            $relatedOriginalPrice = PriceHelper::getDisplayOriginalPrice($relatedMinPrice);
                        @endphp
                        <div
                            class="bg-white rounded-lg shadow-md hover:shadow-lg overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
                            <a href="{{ route('shop.product_overview', $related->id) }}" class="block">
                                <div class="relative pt-[100%]">
                                    <img src="{{ $related->first_image ?? asset('images/logo.svg') }}"
                                        alt="{{ $related->name }}" class="absolute inset-0 w-full h-full object-cover">
                                </div>
                                <div class="p-3">
                                    <h4 class="text-sm font-semibold text-gray-800 mb-1 line-clamp-2">
                                        {{ $related->name }}</h4>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-sm font-bold text-blue-600">
                                            {{ number_format($relatedDiscountedPrice, 0, ',', '.') }}đ
                                        </span>
                                        @if($discountPercent > 0)
                                        <span class="text-xs text-gray-500 line-through">
                                            {{ number_format($relatedOriginalPrice, 0, ',', '.') }}đ
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="relative">
                    <div class="carousel-related overflow-hidden px-4">
                        <div class="carousel-inner flex transition-transform duration-300 ease-in-out gap-4">
                            @foreach ($related_products as $related)
                                @php
                                    $relatedMinPrice = $related->variants->min('price');
                                    $relatedDiscountedPrice = PriceHelper::calculateDiscountedPrice($relatedMinPrice);
                                    $relatedOriginalPrice = PriceHelper::getDisplayOriginalPrice($relatedMinPrice);
                                @endphp
                                <div class="carousel-item flex-none w-1/2 md:w-1/4 min-w-[50%] md:min-w-[25%]">
                                    <div
                                        class="bg-white rounded-lg shadow-md hover:shadow-lg overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
                                        <a href="{{ route('shop.product_overview', $related->id) }}" class="block">
                                            <div class="relative pt-[100%]">
                                                <img src="{{ $related->first_image ?? asset('images/logo.svg') }}"
                                                    alt="{{ $related->name }}"
                                                    class="absolute inset-0 w-full h-full object-cover">
                                            </div>
                                            <div class="p-3">
                                                <h4 class="text-sm font-semibold text-gray-800 mb-1 line-clamp-2">
                                                    {{ $related->name }}</h4>
                                                <div class="flex items-baseline gap-1">
                                                    <span class="text-sm font-bold text-blue-600">
                                                        {{ number_format($relatedDiscountedPrice, 0, ',', '.') }}đ
                                                    </span>
                                                    @if($discountPercent > 0)
                                                    <span class="text-xs text-gray-500 line-through">
                                                        {{ number_format($relatedOriginalPrice, 0, ',', '.') }}đ
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex justify-center gap-4 mt-4">
                        <button
                            class="prev-related bg-gray-800 text-white p-2 rounded-full hover:bg-gray-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button
                            class="next-related bg-gray-800 text-white p-2 rounded-full hover:bg-gray-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Sản phẩm cùng thương hiệu -->
    @if ($same_brand_products->isNotEmpty())
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold text-gray-800">Sản phẩm cùng thương hiệu</h3>
                <a href="/catfilter?brand={{ urlencode($product->brand) }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition duration-200">
                    Xem tất cả
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
            @if ($same_brand_products->count() <= 4)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($same_brand_products as $brand_product)
                        @php
                            $brandProductMinPrice = $brand_product->variants->min('price');
                            $brandProductDiscountedPrice = PriceHelper::calculateDiscountedPrice($brandProductMinPrice);
                            $brandProductOriginalPrice = PriceHelper::getDisplayOriginalPrice($brandProductMinPrice);
                        @endphp
                        <div
                            class="bg-white rounded-lg shadow-md hover:shadow-lg overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
                            <a href="{{ route('shop.product_overview', $brand_product->id) }}" class="block">
                                <div class="relative pt-[100%]">
                                    <img src="{{ $brand_product->first_image ?? asset('images/logo.svg') }}"
                                        alt="{{ $brand_product->name }}"
                                        class="absolute inset-0 w-full h-full object-cover">
                                </div>
                                <div class="p-3">
                                    <h4 class="text-sm font-semibold text-gray-800 mb-1 line-clamp-2">
                                        {{ $brand_product->name }}</h4>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-sm font-bold text-blue-600">
                                            {{ number_format($brandProductDiscountedPrice, 0, ',', '.') }}đ
                                        </span>
                                        @if($discountPercent > 0)
                                        <span class="text-xs text-gray-500 line-through">
                                            {{ number_format($brandProductOriginalPrice, 0, ',', '.') }}đ
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="relative">
                    <div class="carousel-brand overflow-hidden px-4">
                        <div class="carousel-inner flex transition-transform duration-300 ease-in-out gap-4">
                            @foreach ($same_brand_products as $brand_product)
                                @php
                                    $brandProductMinPrice = $brand_product->variants->min('price');
                                    $brandProductDiscountedPrice = PriceHelper::calculateDiscountedPrice($brandProductMinPrice);
                                    $brandProductOriginalPrice = PriceHelper::getDisplayOriginalPrice($brandProductMinPrice);
                                @endphp
                                <div class="carousel-item flex-none w-1/2 md:w-1/4 min-w-[50%] md:min-w-[25%]">
                                    <div
                                        class="bg-white rounded-lg shadow-md hover:shadow-lg overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
                                        <a href="{{ route('shop.product_overview', $brand_product->id) }}"
                                            class="block">
                                            <div class="relative pt-[100%]">
                                                <img src="{{ $brand_product->first_image ?? asset('images/logo.svg') }}"
                                                    alt="{{ $brand_product->name }}"
                                                    class="absolute inset-0 w-full h-full object-cover">
                                            </div>
                                            <div class="p-3">
                                                <h4 class="text-sm font-semibold text-gray-800 mb-1 line-clamp-2">
                                                    {{ $brand_product->name }}</h4>
                                                <div class="flex items-baseline gap-1">
                                                    <span class="text-sm font-bold text-blue-600">
                                                        {{ number_format($brandProductDiscountedPrice, 0, ',', '.') }}đ
                                                    </span>
                                                    @if($discountPercent > 0)
                                                    <span class="text-xs text-gray-500 line-through">
                                                        {{ number_format($brandProductOriginalPrice, 0, ',', '.') }}đ
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex justify-center gap-4 mt-4">
                        <button
                            class="prev-brand bg-gray-800 text-white p-2 rounded-full hover:bg-gray-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button
                            class="next-brand bg-gray-800 text-white p-2 rounded-full hover:bg-gray-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <script>
        function initCarousel(carouselClass, prevBtnClass, nextBtnClass) {
            const carousel = document.querySelector(carouselClass);
            const inner = carousel.querySelector('.carousel-inner');
            const items = carousel.querySelectorAll('.carousel-item');
            const prevBtn = document.querySelector(prevBtnClass);
            const nextBtn = document.querySelector(nextBtnClass);

            let currentIndex = 0;
            const totalItems = items.length;

            function getItemsPerView() {
                return window.innerWidth >= 768 ? 4 : 2; // 4 on md+, 2 on mobile
            }

            function getItemWidth() {
                return window.innerWidth >= 768 ? 25 : 50; // 25% on md+, 50% on mobile
            }

            function getVisibleItemsOffset() {
                return window.innerWidth >= 768 ? 0.5 : 0.5; // Show 4.5 items on desktop, 2.5 on mobile
            }

            function updateCarousel() {
                const itemsPerView = getItemsPerView();
                const itemWidth = getItemWidth();
                const offset = -currentIndex * itemWidth;
                inner.style.transform = `translateX(${offset}%)`;

                prevBtn.disabled = currentIndex === 0;
                nextBtn.disabled = currentIndex >= totalItems - itemsPerView - getVisibleItemsOffset();
            }

            prevBtn.addEventListener('click', () => {
                if (currentIndex > 0) {
                    currentIndex--;
                    updateCarousel();
                }
            });

            nextBtn.addEventListener('click', () => {
                const itemsPerView = getItemsPerView();
                if (currentIndex < totalItems - itemsPerView - getVisibleItemsOffset()) {
                    currentIndex++;
                    updateCarousel();
                }
            });

            // Initial update
            updateCarousel();

            // Update on resize
            window.addEventListener('resize', updateCarousel);
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (document.querySelector('.carousel-related')) {
                initCarousel('.carousel-related', '.prev-related', '.next-related');
            }
            if (document.querySelector('.carousel-brand')) {
                initCarousel('.carousel-brand', '.prev-brand', '.next-brand');
            }

            Livewire.on('colorSelected', (data) => {
                console.log(data[0]);
            });

            Livewire.on('sizeSelected', (data) => {
                console.log(data[0]);
            });

            Livewire.on('checkcolorfirst', () => {
                alert('Vui lòng chọn phân mục ở trên trước');
            });
        });
    </script>
</div>
