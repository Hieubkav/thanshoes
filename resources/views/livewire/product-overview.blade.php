<div>
    <div class="bg-gray-50 dark:bg-gray-800 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap -mx-4">
                <!-- Phần hình ảnh sản phẩm -->
                @php
                    $productGalleryImages = [];

                    if (!empty($main_image)) {
                        $productGalleryImages[] = $main_image;
                    }

                    foreach ($list_images_product as $galleryItem) {
                        $resolvedImage = $galleryItem->type === 'variant'
                            ? $galleryItem->image
                            : asset('storage/' . $galleryItem->image);

                        if ($resolvedImage && !in_array($resolvedImage, $productGalleryImages, true)) {
                            $productGalleryImages[] = $resolvedImage;
                        }
                    }
                @endphp
                <div class="w-full md:w-1/2 px-4 mb-8">
                    <div class="relative rounded-2xl bg-white dark:bg-gray-900 shadow-lg overflow-hidden">
                        @if ($product->variants->min('price') >= 500000)
                            <div class="absolute top-3 left-3 z-20 bg-blue-500 text-white text-sm font-semibold px-3 py-1 rounded-full shadow-md">
                                Freeship
                            </div>
                        @endif
                        @if($globalDiscountPercent > 0)
                            <div class="absolute top-3 right-3 z-20 bg-red-500 text-white text-sm font-semibold px-3 py-1 rounded-full shadow-md">
                                @if($globalDiscountType === 'percent')
                                    -{{ $globalDiscountPercent }}%
                                @else
                                    -{{ number_format($globalDiscountPercent, 0, ',', '.') }}đ
                                @endif
                            </div>
                        @endif
                        <div id="productImageWrapper"
                            class="flex overflow-x-auto snap-x snap-mandatory scroll-smooth pb-6 touch-pan-x gap-4 px-4 md:hidden"
                            style="-ms-overflow-style: none; scrollbar-width: none;">
                            @foreach ($productGalleryImages as $index => $imageSrc)
                                <figure class="relative flex-shrink-0 w-[82%] snap-center">
                                    <img src="{{ $imageSrc }}"
                                        loading="lazy"
                                        alt="Hình sản phẩm {{ $product->name }}"
                                        class="w-full h-auto object-cover cursor-pointer select-none transition-transform duration-300 hover:scale-[1.01]"
                                        data-gallery-index="{{ $index }}"
                                        draggable="false">
                                </figure>
                            @endforeach
                        </div>
                        <figure class="hidden md:block">
                            <img id="desktopMainImage"
                                src="{{ $productGalleryImages[0] ?? $main_image }}"
                                alt="Hình sản phẩm {{ $product->name }}"
                                loading="lazy"
                                class="w-full h-auto object-cover cursor-pointer select-none transition-transform duration-300 hover:scale-[1.01] rounded-2xl">
                        </figure>
                        <button
                            id="mainSliderPrev"
                            type="button"
                            class="absolute left-3 top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-gray-700 shadow-lg transition hover:bg-white dark:bg-gray-800/90 dark:text-gray-200 md:hidden">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button
                            id="mainSliderNext"
                            type="button"
                            class="absolute right-3 top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-gray-700 shadow-lg transition hover:bg-white dark:bg-gray-800/90 dark:text-gray-200 md:hidden">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                    <div class="hidden md:flex gap-4 py-4 overflow-x-auto">
                        @foreach ($productGalleryImages as $index => $imageSrc)
                            <button type="button"
                                class="desktop-thumbnail relative flex h-24 w-24 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg border-2 border-transparent bg-white opacity-80 shadow transition-all duration-300 hover:border-blue-300 hover:opacity-100"
                                data-desktop-thumbnail="{{ $index }}">
                                <img src="{{ $imageSrc }}"
                                    alt="Ảnh {{ $index + 1 }} - {{ $product->name }}"
                                    loading="lazy"
                                    class="h-full w-full object-cover">
                            </button>
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
        let galleryImages = [];
        let currentGalleryIndex = 0;
        let productImageSlider = null;
        let sliderSlides = [];
        let sliderScrollTimeout = null;
        let isProgrammaticSliderScroll = false;
        let desktopMainImage = null;
        let desktopThumbnails = [];

        document.addEventListener('DOMContentLoaded', function() {
            const initialImages = @json($productGalleryImages);
            galleryImages = Array.isArray(initialImages) ? initialImages.slice() : [];

            registerLivewireEvents();
            initializeMainImageSlider();
            initializeDesktopGallery();
            setupGalleryEventListeners();
        });

        function openImageGalleryFromMain(index) {
            const targetIndex = typeof index === 'number' ? index : currentGalleryIndex;
            openImageGallery(targetIndex);
        }

        function openImageGallery(index = 0) {
            if (!galleryImages.length) {
                return;
            }

            currentGalleryIndex = (index + galleryImages.length) % galleryImages.length;
            const modal = document.getElementById('imageGalleryModal');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            updateGalleryImage();
            document.body.style.overflow = 'hidden';
        }

        function closeImageGallery() {
            const modal = document.getElementById('imageGalleryModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }

        function nextImage() {
            if (!galleryImages.length) {
                return;
            }

            currentGalleryIndex = (currentGalleryIndex + 1) % galleryImages.length;
            updateGalleryImage();
        }

        function previousImage() {
            if (!galleryImages.length) {
                return;
            }

            currentGalleryIndex = (currentGalleryIndex - 1 + galleryImages.length) % galleryImages.length;
            updateGalleryImage();
        }

        function updateGalleryImage() {
            if (!galleryImages.length) {
                return;
            }

            const mainImage = document.getElementById('galleryMainImage');
            const currentIndexSpan = document.getElementById('currentImageIndex');
            const totalImagesSpan = document.getElementById('totalImages');

            mainImage.src = galleryImages[currentGalleryIndex];
            currentIndexSpan.textContent = currentGalleryIndex + 1;
            totalImagesSpan.textContent = galleryImages.length;

            updateGalleryThumbnails();
            syncSliderWithGallery();
        }

        function updateGalleryThumbnails() {
            const thumbnailContainer = document.getElementById('galleryThumbnails');
            if (!thumbnailContainer) {
                return;
            }

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
            document.addEventListener('keydown', function(e) {
                const modal = document.getElementById('imageGalleryModal');
                if (!modal || modal.classList.contains('hidden')) {
                    return;
                }

                switch (e.key) {
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
            });

            const modal = document.getElementById('imageGalleryModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeImageGallery();
                    }
                });
            }

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

        function initializeMainImageSlider() {
            productImageSlider = document.getElementById('productImageWrapper');
            const prevButton = document.getElementById('mainSliderPrev');
            const nextButton = document.getElementById('mainSliderNext');

            if (!productImageSlider) {
                return;
            }

            sliderSlides = Array.from(productImageSlider.querySelectorAll('[data-gallery-index]'));
            attachSliderTapHandlers();

            if (sliderSlides.length <= 1) {
                if (prevButton) {
                    prevButton.style.display = 'none';
                }
                if (nextButton) {
                    nextButton.style.display = 'none';
                }
            } else {
                if (prevButton) {
                    prevButton.style.display = '';
                    prevButton.addEventListener('click', () => navigateMainSlider(-1));
                }
                if (nextButton) {
                    nextButton.style.display = '';
                    nextButton.addEventListener('click', () => navigateMainSlider(1));
                }
            }

            updateMainSliderState(0);

            productImageSlider.addEventListener('scroll', () => {
                if (isProgrammaticSliderScroll) {
                    return;
                }

                if (sliderScrollTimeout) {
                    clearTimeout(sliderScrollTimeout);
                }

                sliderScrollTimeout = setTimeout(() => {
                    const activeIndex = findActiveSliderIndex();
                    updateMainSliderState(activeIndex);
                }, 120);
            }, { passive: true });
        }

        function updateMainSliderState(index) {
            if (!sliderSlides.length) {
                if (galleryImages.length) {
                    currentGalleryIndex = Math.max(0, Math.min(index, galleryImages.length - 1));
                    updateDesktopImage(currentGalleryIndex);
                }
                return;
            }

            const boundedIndex = Math.max(0, Math.min(index, sliderSlides.length - 1));
            currentGalleryIndex = boundedIndex;
            updateDesktopImage(boundedIndex);
        }

        function scrollMainSliderTo(index, options = {}) {
            if (!productImageSlider || !sliderSlides.length) {
                return;
            }

            const slideCount = sliderSlides.length;
            const safeIndex = ((index % slideCount) + slideCount) % slideCount;
            const targetSlide = sliderSlides[safeIndex];

            if (!targetSlide) {
                return;
            }

            const behavior = options.behavior || 'smooth';

            isProgrammaticSliderScroll = true;
            productImageSlider.scrollTo({
                left: targetSlide.offsetLeft,
                behavior
            });
            updateMainSliderState(safeIndex);

            setTimeout(() => {
                isProgrammaticSliderScroll = false;
            }, options.resetDelay || 300);
        }

        function navigateMainSlider(step) {
            if (!sliderSlides.length) {
                return;
            }

            const nextIndex = currentGalleryIndex + step;
            scrollMainSliderTo(nextIndex);
        }

        function findActiveSliderIndex() {
            if (!productImageSlider || !sliderSlides.length) {
                return 0;
            }

            let activeIndex = 0;
            let minDistance = Infinity;
            const center = productImageSlider.scrollLeft + productImageSlider.clientWidth / 2;

            sliderSlides.forEach((slide, index) => {
                const slideCenter = slide.offsetLeft + slide.offsetWidth / 2;
                const distance = Math.abs(center - slideCenter);

                if (distance < minDistance) {
                    minDistance = distance;
                    activeIndex = index;
                }
            });

            return activeIndex;
        }

        function syncSliderWithGallery() {
            updateDesktopImage(currentGalleryIndex);

            if (!productImageSlider || !sliderSlides.length) {
                return;
            }
            scrollMainSliderTo(currentGalleryIndex, { behavior: 'smooth', resetDelay: 200 });
        }

        function attachSliderTapHandlers() {
            if (!sliderSlides.length) {
                return;
            }

            sliderSlides.forEach((slide) => {
                let touchStartX = 0;
                let touchStartY = 0;
                let hasMoved = false;
                let suppressClick = false;

                slide.addEventListener('touchstart', (event) => {
                    if (event.touches.length !== 1) {
                        return;
                    }
                    touchStartX = event.touches[0].clientX;
                    touchStartY = event.touches[0].clientY;
                    hasMoved = false;
                }, { passive: true });

                slide.addEventListener('touchmove', (event) => {
                    if (event.touches.length !== 1) {
                        return;
                    }
                    const deltaX = Math.abs(event.touches[0].clientX - touchStartX);
                    const deltaY = Math.abs(event.touches[0].clientY - touchStartY);
                    if (deltaX > 10 || deltaY > 10) {
                        hasMoved = true;
                    }
                }, { passive: true });

                slide.addEventListener('touchend', (event) => {
                    if (!hasMoved) {
                        event.preventDefault();
                        suppressClick = true;
                        const index = Number(slide.dataset.galleryIndex || 0);
                        openImageGalleryFromMain(index);
                    }
                    hasMoved = false;
                });

                slide.addEventListener('touchcancel', () => {
                    hasMoved = false;
                });

                slide.addEventListener('click', (event) => {
                    if (suppressClick) {
                        suppressClick = false;
                        event.preventDefault();
                        return;
                    }

                    if (!hasMoved) {
                        const index = Number(slide.dataset.galleryIndex || 0);
                        openImageGalleryFromMain(index);
                    }

                    hasMoved = false;
                });
            });
        }

        function initializeDesktopGallery() {
            desktopMainImage = document.getElementById('desktopMainImage');
            desktopThumbnails = Array.from(document.querySelectorAll('[data-desktop-thumbnail]'));

            if (desktopMainImage) {
                desktopMainImage.addEventListener('click', () => openImageGallery(currentGalleryIndex));
            }

            if (desktopThumbnails.length) {
                desktopThumbnails.forEach((thumbnail) => {
                    thumbnail.addEventListener('click', () => {
                        const index = Number(thumbnail.dataset.desktopThumbnail || 0);
                        setActiveDesktopImage(index);
                    });
                });
            }

            updateDesktopImage(currentGalleryIndex);
        }

        function setActiveDesktopImage(index) {
            if (!galleryImages.length) {
                return;
            }

            const imageCount = galleryImages.length;
            const safeIndex = ((index % imageCount) + imageCount) % imageCount;
            currentGalleryIndex = safeIndex;

            updateDesktopImage(safeIndex);

            if (productImageSlider && sliderSlides.length) {
                scrollMainSliderTo(safeIndex, { behavior: 'smooth' });
            }
        }

        function updateDesktopImage(index) {
            if (!galleryImages.length) {
                return;
            }

            const boundedIndex = Math.max(0, Math.min(index, galleryImages.length - 1));
            const targetSrc = galleryImages[boundedIndex];

            if (desktopMainImage && targetSrc) {
                desktopMainImage.src = targetSrc;
            }

            if (desktopThumbnails.length) {
                desktopThumbnails.forEach((thumbnail, thumbIndex) => {
                    const isActive = thumbIndex === boundedIndex;
                    thumbnail.classList.toggle('border-blue-500', isActive);
                    thumbnail.classList.toggle('opacity-100', isActive);
                    thumbnail.classList.toggle('shadow-lg', isActive);
                    thumbnail.classList.toggle('border-transparent', !isActive);
                    thumbnail.classList.toggle('opacity-80', !isActive);
                    thumbnail.classList.toggle('shadow', !isActive);
                });
            }
        }

        function registerLivewireEvents() {
            Livewire.on('colorSelected', (data) => {
                console.log(data[0]);
            });

            Livewire.on('sizeSelected', (data) => {
                console.log(data[0]);
            });

            Livewire.on('checkcolorfirst', () => {
                alert('Vui long chon phan muc o tren truoc');
            });
        }

        let touchStartX = 0;
        let touchEndX = 0;

        function handleSwipe() {
            const swipeThreshold = 50;
            const swipeDistance = touchEndX - touchStartX;

            if (Math.abs(swipeDistance) > swipeThreshold) {
                if (swipeDistance > 0) {
                    previousImage();
                } else {
                    nextImage();
                }
            }
        }
    </script>
@endpush

