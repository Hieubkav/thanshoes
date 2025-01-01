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
                                    bg-white text-gray-800 hover:shadow-md hover:border-blue-500 hover:text-blue-600 @endif
                                @if ($countfilter == 2 and $color == $selectedColor) bg-blue-500 text-blue-400 font-bold shadow-md @endif">
                                        <input wire:model.live.debounce.300ms="selectedColor"
                                            @if ($product->variants->where('color', $color)->sum('stock') == 0) disabled @endif
                                            value="{{ $color }}" type="radio" name="color"
                                            class="absolute opacity-0 w-0 h-0">
                                        <div
                                            class="w-12 h-12 flex items-center justify-center bg-gray-100 rounded-full shadow-sm overflow-hidden">
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
                                            bg-gradient-to-r from-green-400 via-green-500 to-green-600 text-black hover:shadow-md @endif
                                        @if ($size == $selectedSize) bg-green-700 border-2 border-green-500 shadow-lg font-bold @endif">
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
