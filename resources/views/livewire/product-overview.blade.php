<div>
    <div class="bg-gray-100 dark:bg-gray-800 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap -mx-4">
                <!-- Phần hình ảnh sản phẩm -->
                <div class="w-full md:w-1/2 px-4 mb-8 ">
                    <div class="relative">
                        <img id="mainImage" src="{{ $main_image }}" alt="Product Image"
                            class=" w-full h-auto rounded-lg shadow-md mb-4 object-cover">
                        @if ($product->variants->min('price') >= 500000)
                            <div class="absolute top-0 left-0 bg-blue-500 text-white px-2 py-1 rounded-br-lg">
                                Freeship
                            </div>
                        @endif
                        <div class="absolute top-0 right-0 bg-red-500 text-white px-2 py-1 rounded-bl-lg">
                            -49%
                        </div>
                    </div>
                    <div class="flex gap-4 py-4 overflow-x-auto">
                        @foreach ($list_images_variants as $item)
                            <img src="{{ $item }}"
                                class="w-20 h-20 object-cover rounded-md cursor-pointer opacity-60 hover:opacity-100 transition duration-300"
                                onclick="changeImage(this.src)">
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
                                {{ number_format($product->variants->min('price'), 0, ',', '.') }}vnd
                            @else
                                {{ number_format($product->variants->min('price'), 0, ',', '.') }}vnd
                                -
                                {{ number_format($product->variants->max('price'), 0, ',', '.') }}vnd
                            @endif
                        </span>
                        <span class="text-gray-500 line-through italic">
                            {{ number_format(($product->variants->max('price') * 149) / 100, 0, ',', '.') }}vnd
                        </span>
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
                                                    ->filter(fn($variant) => $variant->variant_images->isNotEmpty())
                                                    ->random()
                                                    ?->variant_images->random(),
                                            )->image ?? asset('images/logo.svg');
                                    @endphp
                                    <label
                                        class="group relative flex flex-col items-center justify-center p-3 border rounded-lg cursor-pointer shadow-sm
                                @if ($product->variants->where('color', $color)->sum('stock') == 0) bg-gray-200 text-gray-400 cursor-not-allowed
                                @else
                                    bg-gray-100 text-gray-800 hover:shadow-[inset_-2px_-2px_10px_rgba(255,255,255,0.8),inset_2px_2px_10px_rgba(0,0,0,0.1)] transition-all duration-300 @endif
                                @if ($countfilter == 2 and $color == $selectedColor) bg-white shadow-[inset_-4px_-4px_12px_rgba(255,255,255,0.9),inset_4px_4px_12px_rgba(0,0,0,0.1)] text-blue-600 font-bold @endif">
                                        <input wire:model.live.debounce.300ms="selectedColor"
                                            @if ($product->variants->where('color', $color)->sum('stock') == 0) disabled @endif
                                            value="{{ $color }}" type="radio" name="color"
                                            class="absolute opacity-0 w-0 h-0">
                                        <div
                                            class="w-12 h-12 flex items-center justify-center bg-gray-100 rounded-full overflow-hidden shadow-[inset_-2px_-2px_6px_rgba(255,255,255,0.7),inset_2px_2px_6px_rgba(0,0,0,0.1)]">
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
                                    <label
                                        class="group relative flex items-center justify-center p-2 border rounded-lg cursor-pointer shadow-sm
                                        @if (
                                            $countfilter == 1 and $product->variants->where('size', $size)->sum('stock') == 0 or
                                                $countfilter == 2 and
                                                    $product->variants->where('size', $size)->where('color', $selectedColor)->sum('stock') == 0) bg-gray-200 text-gray-400 cursor-not-allowed
                                        @else
                                            bg-gray-100 text-gray-800 hover:shadow-[inset_-2px_-2px_10px_rgba(255,255,255,0.8),inset_2px_2px_10px_rgba(0,0,0,0.1)] transition-all duration-300 @endif
                                        @if ($size == $selectedSize) bg-white shadow-[inset_-4px_-4px_12px_rgba(255,255,255,0.9),inset_4px_4px_12px_rgba(0,0,0,0.1)] text-green-600 font-bold @endif">
                                        <input wire:model.live.debounce.300ms="selectedSize"
                                            @if (
                                                $countfilter == 1 and $product->variants->where('size', $size)->sum('stock') == 0 or
                                                    $countfilter == 2 and
                                                        $product->variants->where('size', $size)->where('color', $selectedColor)->sum('stock') == 0) disabled @endif
                                            value="{{ $size }}" type="radio" name="size"
                                            class="absolute opacity-0 w-0 h-0">
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


                    <!-- Nút Thêm vào giỏ hàng -->
                    <div class="flex space-x-4 mb-6" >

                        @include('partials.button_add_cart')

                    </div>

                </div>
            </div>
        </div>

        <!-- Mô tả sản phẩm -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Mô tả sản phẩm</h3>
            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                {!! $product->description !!}
            </p>
        </div>
    </div>

    <!-- Sản phẩm cùng danh mục -->
    @if($related_products->isNotEmpty())
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-semibold text-gray-800">Sản phẩm cùng danh mục</h3>
            <a href="/catfilter?type={{ urlencode($product->type) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200">
                Xem tất cả
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($related_products as $related)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <a href="{{ route('shop.product_overview', $related->id) }}" class="block">
                    <div class="relative pt-[100%]">
                        <img src="{{ $related->first_image ?? asset('images/logo.svg') }}" 
                             alt="{{ $related->name }}"
                             class="absolute top-0 left-0 w-full h-full object-cover">
                    </div>
                    <div class="p-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">{{ $related->name }}</h4>
                        <div class="flex items-baseline gap-2">
                            <span class="text-lg font-bold text-blue-600">
                                {{ number_format($related->variants->min('price'), 0, ',', '.') }}đ
                            </span>
                            <span class="text-sm text-gray-500 line-through">
                                {{ number_format(($related->variants->min('price') * 149) / 100, 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Sản phẩm cùng thương hiệu -->
    @if($same_brand_products->isNotEmpty())
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-semibold text-gray-800">Sản phẩm cùng thương hiệu</h3>
            <a href="/catfilter?brand={{ urlencode($product->brand) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200">
                Xem tất cả
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($same_brand_products as $brand_product)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300">
                <a href="{{ route('shop.product_overview', $brand_product->id) }}" class="block">
                    <div class="relative pt-[100%]">
                        <img src="{{ $brand_product->first_image ?? asset('images/logo.svg') }}" 
                             alt="{{ $brand_product->name }}"
                             class="absolute top-0 left-0 w-full h-full object-cover">
                    </div>
                    <div class="p-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">{{ $brand_product->name }}</h4>
                        <div class="flex items-baseline gap-2">
                            <span class="text-lg font-bold text-blue-600">
                                {{ number_format($brand_product->variants->min('price'), 0, ',', '.') }}đ
                            </span>
                            <span class="text-sm text-gray-500 line-through">
                                {{ number_format(($brand_product->variants->min('price') * 149) / 100, 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

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

            Livewire.on('checkcolorfirst', (data) => {
                alert('Vui lòng chọn phân mục ở trên trước');
            });
        });
    </script>

</div>
