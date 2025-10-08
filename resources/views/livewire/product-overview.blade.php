<div>
    <div class="bg-gray-50 dark:bg-gray-800 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap -mx-4">
                <!-- Phần hình ảnh sản phẩm -->
                <div class="w-full md:w-1/2 px-4 mb-8">
                    <div class="relative">
                        <img id="mainImage" src="{{ $main_image }}" alt="Product Image"
                            loading="lazy"
                            class="w-full h-auto rounded-lg shadow-md mb-4 object-cover cursor-pointer"
                            onclick="openImageGalleryFromMain()">
                        @if ($product->variants->min('price') >= 500000)
                            <div class="absolute top-0 left-0 bg-blue-500 text-white px-2 py-1 rounded-br-lg">
                                Freeship
                            </div>
                        @endif
                        @if($globalDiscountPercent > 0)
                        <div class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 rounded-bl-lg">
                            @if($globalDiscountType === 'percent')
                                -{{ $globalDiscountPercent }}%
                            @else
                                -{{ number_format($globalDiscountPercent, 0, ',', '.') }}đ
                            @endif
                        </div>
                        @endif
                    </div>
                    <div class="flex gap-4 py-4 overflow-x-auto">
                        @foreach ($list_images_product as $index => $item)
                            @if ($item->type == 'variant')
                                <img src="{{ $item->image }}"
                                    loading="lazy"
                                    class="w-20 h-20 object-cover rounded-md hover:opacity-90 transition duration-300 cursor-pointer border-2 border-transparent hover:border-blue-300"
                                    onclick="changeImage('{{ $item->image }}')"
                                    data-gallery-index="{{ $index }}">
                            @else
                                <img src="{{ asset('storage/' . $item->image) }}"
                                    loading="lazy"
                                    class="w-20 h-20 object-cover rounded-md hover:opacity-90 transition duration-300 cursor-pointer border-2 border-transparent hover:border-blue-300"
                                    onclick="changeImage('{{ asset('storage/' . $item->image) }}')"
                                    data-gallery-index="{{ $index }}">
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
                                    $discountedPrice = \App\Helpers\PriceHelper::calculateDiscountedPrice($price);
                                @endphp
                                {{ number_format($discountedPrice, 0, ',', '.') }}vnd
                            @else
                                @php
                                    $minPrice = $product->variants->min('price');
                                    $maxPrice = $product->variants->max('price');
                                    $discountedMinPrice = \App\Helpers\PriceHelper::calculateDiscountedPrice($minPrice);
                                    $discountedMaxPrice = \App\Helpers\PriceHelper::calculateDiscountedPrice($maxPrice);
                                @endphp
                                {{ number_format($discountedMinPrice, 0, ',', '.') }}vnd
                                -
                                {{ number_format($discountedMaxPrice, 0, ',', '.') }}vnd
                            @endif
                        </span>
                        @if($globalDiscountPercent > 0)
                        <span class="text-gray-500 line-through italic">
                            @php
                                $displayOriginalPrice = \App\Helpers\PriceHelper::getDisplayOriginalPrice($product->variants->max('price'));
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
                                                loading="lazy"
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
                    <div class="flex items-start space-x-4 mb-6 hidden md:flex" x-data="{ showSizeModal: false }">
                        <div class="w-full max-w-full md:max-w-[220px] flex flex-col gap-2 flex-shrink-0">
                            <button type="button"
                                    wire:click="openQuickBuy"
                                    wire:loading.attr="disabled"
                                    wire:target="openQuickBuy"
                                    class="w-full px-4 py-3 text-white font-semibold rounded-lg shadow-md bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-400 transition-all duration-300 text-center disabled:opacity-60 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="openQuickBuy">Mua ngay</span>
                                <span wire:loading wire:target="openQuickBuy" class="inline-flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V2a10 10 0 100 20v-2a8 8 0 01-8-8z"></path>
                                    </svg>
                                    <span>Dang mo</span>
                                </span>
                            </button>
                            @include('partials.button_add_cart')
                        </div>
                        @if($sizeShoesImage)
                            <div class="relative">
                                <button type="button"
                                    @click="showSizeModal = true"
                                    class="group relative rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                                    <img src="{{ asset('storage/' . $sizeShoesImage) }}"
                                        alt="Bảng size giày"
                                        loading="lazy"
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
                                                        <img src="{{ asset('storage/' . $sizeShoesImage) }}"
                                                            alt="Bảng size giày"
                                                            loading="lazy"
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

                    <!-- Mobile Size Chart Button -->
                    <div class="md:hidden mb-6">
                        @if($sizeShoesImage)
                            <div class="flex justify-center" x-data="{ showSizeModal: false }">
                                <button type="button"
                                    @click="showSizeModal = true"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Xem bảng size</span>
                                </button>

                                <!-- Mobile Size Modal -->
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

                                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full mx-4">
                                            <div class="bg-white p-4">
                                                <div class="flex justify-between items-center mb-4">
                                                    <h3 class="text-lg font-medium text-gray-900">
                                                        Bảng Size Giày
                                                    </h3>
                                                    <button type="button"
                                                        @click="showSizeModal = false"
                                                        class="text-gray-400 hover:text-gray-500">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <img src="{{ asset('storage/' . $sizeShoesImage) }}"
                                                    alt="Bảng size giày"
                                                    loading="lazy"
                                                    class="w-full h-auto rounded-lg">
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

    <!-- Image Gallery Modal (inside main div) -->
    <div id="imageGalleryModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center">
        <div class="relative max-w-4xl max-h-full w-full h-full flex items-center justify-center p-4">
            <!-- Close button -->
            <button onclick="closeImageGallery()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-2 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Previous button -->
            <button onclick="previousImage()" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-3 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <!-- Next button -->
            <button onclick="nextImage()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 z-10 bg-black bg-opacity-50 rounded-full p-3 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Main image -->
            <img id="galleryMainImage" src="" alt="Product Image" class="max-w-full max-h-full object-contain rounded-lg">

            <!-- Image counter -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white bg-black bg-opacity-50 px-3 py-1 rounded-full text-sm">
                <span id="currentImageIndex">1</span> / <span id="totalImages">1</span>
            </div>

            <!-- Thumbnail navigation -->
            <div class="absolute bottom-16 left-1/2 transform -translate-x-1/2 flex gap-2 max-w-full overflow-x-auto px-4" id="galleryThumbnails">
                <!-- Thumbnails will be populated by JavaScript -->
            </div>
        </div>
    </div>




    <style>
        /* Gallery Modal Styles */
        #imageGalleryModal {
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease-out;
        }

        #imageGalleryModal.hidden {
            animation: fadeOut 0.3s ease-out;
        }

        #galleryMainImage {
            max-height: 80vh;
            transition: opacity 0.3s ease-in-out;
        }

        #galleryThumbnails {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }

        #galleryThumbnails::-webkit-scrollbar {
            height: 4px;
        }

        #galleryThumbnails::-webkit-scrollbar-track {
            background: transparent;
        }

        #galleryThumbnails::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        /* Button hover effects */
        #imageGalleryModal button:hover {
            transform: scale(1.05);
        }

        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            #galleryThumbnails {
                bottom: 8px;
                padding: 0 8px;
            }

            #imageGalleryModal .absolute.bottom-4 {
                bottom: 16px;
            }

            #imageGalleryModal .absolute.left-4,
            #imageGalleryModal .absolute.right-4 {
                padding: 8px;
            }

            #galleryMainImage {
                max-height: 70vh;
            }
        }

        /* Touch gestures for mobile */
        #galleryMainImage {
            touch-action: pan-x;
        }
    </style>
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
                            $relatedDiscountedPrice = \App\Helpers\PriceHelper::calculateDiscountedPrice($relatedMinPrice);
                            $relatedOriginalPrice = \App\Helpers\PriceHelper::getDisplayOriginalPrice($relatedMinPrice);
                        @endphp
                        <div
                            class="bg-white rounded-lg shadow-md hover:shadow-lg overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
                            <a href="{{ route('shop.product_overview', $related->slug) }}" class="block">
                                <div class="relative pt-[100%]">
                                    <img src="{{ $related->first_image ?? asset('images/logo.svg') }}"
                                        alt="{{ $related->name }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover">
                                </div>
                                <div class="p-3">
                                    <h4 class="text-sm font-semibold text-gray-800 mb-1 line-clamp-2">
                                        {{ $related->name }}</h4>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-sm font-bold text-blue-600">
                                            {{ number_format($relatedDiscountedPrice, 0, ',', '.') }}đ
                                        </span>
                                        @if($globalDiscountPercent > 0)
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
                                    $relatedDiscountedPrice = \App\Helpers\PriceHelper::calculateDiscountedPrice($relatedMinPrice);
                                    $relatedOriginalPrice = \App\Helpers\PriceHelper::getDisplayOriginalPrice($relatedMinPrice);
                                @endphp
                                <div class="carousel-item flex-none w-1/2 md:w-1/4 min-w-[50%] md:min-w-[25%]">
                                    <div
                                        class="bg-white rounded-lg shadow-md hover:shadow-lg overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
                                        <a href="{{ route('shop.product_overview', $related->slug) }}" class="block">
                                            <div class="relative pt-[100%]">
                                                <img src="{{ $related->first_image ?? asset('images/logo.svg') }}"
                                                    alt="{{ $related->name }}"
                                                    loading="lazy"
                                                    class="absolute inset-0 w-full h-full object-cover">
                                            </div>
                                            <div class="p-3">
                                                <h4 class="text-sm font-semibold text-gray-800 mb-1 line-clamp-2">
                                                    {{ $related->name }}</h4>
                                                <div class="flex items-baseline gap-1">
                                                    <span class="text-sm font-bold text-blue-600">
                                                        {{ number_format($relatedDiscountedPrice, 0, ',', '.') }}đ
                                                    </span>
                                                    @if($globalDiscountPercent > 0)
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
                            $brandProductDiscountedPrice = \App\Helpers\PriceHelper::calculateDiscountedPrice($brandProductMinPrice);
                            $brandProductOriginalPrice = \App\Helpers\PriceHelper::getDisplayOriginalPrice($brandProductMinPrice);
                        @endphp
                        <div
                            class="bg-white rounded-lg shadow-md hover:shadow-lg overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
                            <a href="{{ route('shop.product_overview', $brand_product->slug) }}" class="block">
                                <div class="relative pt-[100%]">
                                    <img src="{{ $brand_product->first_image ?? asset('images/logo.svg') }}"
                                        alt="{{ $brand_product->name }}"
                                        loading="lazy"
                                        class="absolute inset-0 w-full h-full object-cover">
                                </div>
                                <div class="p-3">
                                    <h4 class="text-sm font-semibold text-gray-800 mb-1 line-clamp-2">
                                        {{ $brand_product->name }}</h4>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-sm font-bold text-blue-600">
                                            {{ number_format($brandProductDiscountedPrice, 0, ',', '.') }}đ
                                        </span>
                                        @if($globalDiscountPercent > 0)
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
                                    $brandProductDiscountedPrice = \App\Helpers\PriceHelper::calculateDiscountedPrice($brandProductMinPrice);
                                    $brandProductOriginalPrice = \App\Helpers\PriceHelper::getDisplayOriginalPrice($brandProductMinPrice);
                                @endphp
                                <div class="carousel-item flex-none w-1/2 md:w-1/4 min-w-[50%] md:min-w-[25%]">
                                    <div
                                        class="bg-white rounded-lg shadow-md hover:shadow-lg overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
                                        <a href="{{ route('shop.product_overview', $brand_product->slug) }}"
                                            class="block">
                                            <div class="relative pt-[100%]">
                                                <img src="{{ $brand_product->first_image ?? asset('images/logo.svg') }}"
                                                    alt="{{ $brand_product->name }}"
                                                    loading="lazy"
                                                    class="absolute inset-0 w-full h-full object-cover">
                                            </div>
                                            <div class="p-3">
                                                <h4 class="text-sm font-semibold text-gray-800 mb-1 line-clamp-2">
                                                    {{ $brand_product->name }}</h4>
                                                <div class="flex items-baseline gap-1">
                                                    <span class="text-sm font-bold text-blue-600">
                                                        {{ number_format($brandProductDiscountedPrice, 0, ',', '.') }}đ
                                                    </span>
                                                    @if($globalDiscountPercent > 0)
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

    


    @include('partials.quick_buy_modal')

<!-- Mobile Bottom Actions -->
@include('partials.product_bottom_actions')

</div>

@push('scripts')
<script>
        // Gallery data
        let galleryImages = [];
        let currentGalleryIndex = 0;

        // Initialize gallery images from PHP data
        document.addEventListener('DOMContentLoaded', function() {
            // Collect all product images including main image
            galleryImages = [];

            // Add main image first if it exists
            const mainImageSrc = "{{ $main_image }}";
            if (mainImageSrc) {
                galleryImages.push(mainImageSrc);
            }

            // Add other product images
            const productImages = [
                @foreach ($list_images_product as $item)
                    @if ($item->type == 'variant')
                        "{{ $item->image }}",
                    @else
                        "{{ asset('storage/' . $item->image) }}",
                    @endif
                @endforeach
            ];

            // Add unique images only
            productImages.forEach(img => {
                if (img && !galleryImages.includes(img)) {
                    galleryImages.push(img);
                }
            });

            // Register Livewire event hooks
            Livewire.on('colorSelected', (data) => {
                console.log(data[0]);
            });

            Livewire.on('sizeSelected', (data) => {
                console.log(data[0]);
            });

            Livewire.on('checkcolorfirst', () => {
                alert('Vui lòng ch?n phân m?c ? trên tru?c');
            });

            // Setup event listeners
            setupGalleryEventListeners();
        });

        function changeImage(src) {
            document.getElementById('mainImage').src = src;
        }

        function openImageGalleryFromMain() {
            // Find the index of current main image in gallery
            const mainImageSrc = document.getElementById('mainImage').src;
            const index = galleryImages.findIndex(img => img === mainImageSrc);
            openImageGallery(index >= 0 ? index : 0);
        }

        function openImageGallery(index = 0) {
            currentGalleryIndex = index;
            const modal = document.getElementById('imageGalleryModal');
            const mainImage = document.getElementById('galleryMainImage');

            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Update image and counter
            updateGalleryImage();

            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }

        function closeImageGallery() {
            const modal = document.getElementById('imageGalleryModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            // Restore body scroll
            document.body.style.overflow = 'auto';
        }

        function nextImage() {
            currentGalleryIndex = (currentGalleryIndex + 1) % galleryImages.length;
            updateGalleryImage();
        }

        function previousImage() {
            currentGalleryIndex = (currentGalleryIndex - 1 + galleryImages.length) % galleryImages.length;
            updateGalleryImage();
        }

        function updateGalleryImage() {
            const mainImage = document.getElementById('galleryMainImage');
            const currentIndexSpan = document.getElementById('currentImageIndex');
            const totalImagesSpan = document.getElementById('totalImages');

            // Update main image
            mainImage.src = galleryImages[currentGalleryIndex];

            // Update counter
            currentIndexSpan.textContent = currentGalleryIndex + 1;
            totalImagesSpan.textContent = galleryImages.length;

            // Update thumbnails
            updateGalleryThumbnails();
        }

        function updateGalleryThumbnails() {
            const thumbnailContainer = document.getElementById('galleryThumbnails');
            thumbnailContainer.innerHTML = '';

            galleryImages.forEach((imageSrc, index) => {
                const thumbnail = document.createElement('img');
                thumbnail.src = imageSrc;
                thumbnail.className = `w-12 h-12 object-cover rounded cursor-pointer border-2 transition-all ${
                    index === currentGalleryIndex ? 'border-white' : 'border-transparent hover:border-gray-300'
                }`;
                thumbnail.onclick = () => {
                    currentGalleryIndex = index;
                    updateGalleryImage();
                };
                thumbnailContainer.appendChild(thumbnail);
            });
        }

        function setupGalleryEventListeners() {
            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                const modal = document.getElementById('imageGalleryModal');
                if (!modal.classList.contains('hidden')) {
                    switch(e.key) {
                        case 'Escape':
                            closeImageGallery();
                            break;
                        case 'ArrowLeft':
                            previousImage();
                            break;
                        case 'ArrowRight':
                            nextImage();
                            break;
                    }
                }
            });

            // Close modal when clicking outside the image
            const modal = document.getElementById('imageGalleryModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeImageGallery();
                    }
                });
            }

            // Touch swipe support for mobile
            const galleryMainImage = document.getElementById('galleryMainImage');
            if (galleryMainImage) {
                galleryMainImage.addEventListener('touchstart', function(e) {
                    touchStartX = e.changedTouches[0].screenX;
                });

                galleryMainImage.addEventListener('touchend', function(e) {
                    touchEndX = e.changedTouches[0].screenX;
                    handleSwipe();
                });
            }
        }

        // Touch swipe variables
        let touchStartX = 0;
        let touchEndX = 0;

        function handleSwipe() {
            const swipeThreshold = 50;
            const swipeDistance = touchEndX - touchStartX;

            if (Math.abs(swipeDistance) > swipeThreshold) {
                if (swipeDistance > 0) {
                    // Swipe right - previous image
                    previousImage();
                } else {
                    // Swipe left - next image
                    nextImage();
                }
            }
        }
    </script>
@endpush
