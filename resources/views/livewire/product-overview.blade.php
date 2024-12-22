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

                    <!-- Chọn màu -->
                    @if (count($list_colors) > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-blue-600 dark:text-white mb-2 italic">Chọn phân loại ngay:</h3>
                            <div class="flex space-x-4">
                                <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                    @foreach ($list_colors as $color)
                                        <label
                                            class="relative  rounded-md border-none py-1 px-1 text-sm font-semibold uppercase cursor-pointer group text-center
                                            @if ($product->variants->where('color', $color)->sum('stock') == 0) bg-gray-400
                                                cursor-not-allowed
                                            @else
                                                bg-gradient-to-r from-blue-400 via-blue-500 to-blue-600
                                                cursor-pointer @endif

                                            @if ($countfilter == 2 and $color == $selectedColor) text-white font-extrabold shadow-blue-600 shadow-2xl bg-blue-900 border-2 border-blue-500 @endif
                                            ">
                                            <input wire:model.live.debounce.300ms='selectedColor'
                                                @if ($product->variants->where('color', $color)->sum('stock') == 0) disabled @endif
                                                value="{{ $color }}" type="radio" name="color"
                                                class="absolute opacity-0 w-0 h-0">
                                            <span class=" flex items-center justify-center">
                                                {{ $color }}
                                            </span>

                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    

                    <!-- Chọn kích cỡ -->
                    @if (count($list_sizes) > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-green-400 italic dark:text-white mb-2">Chọn phân loại ngay:</h3>
                            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @foreach ($list_sizes as $size)
                                    <label
                                        class="relative  rounded-md border-none py-1 px-1 inline-block text-sm font-semibold group text-center
                                        @if (
                                            $product->variants->where('size', $size)->sum('stock') == 0 or
                                            ($countfilter == 2 and $product->variants->where('size', $size)->where('color', $selectedColor)->sum('stock') == 0)
                                        ) 
                                            bg-gray-400
                                            cursor-not-allowed
                                        @else
                                            bg-gradient-to-r from-green-400 via-green-500 to-green-600
                                            cursor-pointer @endif

                                        @if ($size == $selectedSize) text-white font-extrabold shadow-green-600 shadow-2xl bg-green-900 border-2 border-green-500 @endif
                                        ">
                                        <input wire:model.live.debounce.300ms='selectedSize'
                                            @if ($product->variants->where('size', $size)->sum('stock') == 0 or 
                                            ($countfilter == 2 and $product->variants->where('size', $size)->where('color', $selectedColor)->sum('stock') == 0)
                                            ) disabled @endif
                                            value="{{ $size }}" type="radio" name="size"
                                            class="absolute opacity-0 w-0 h-0">
                                        <span class="flex items-center justify-center">
                                            {{ $size }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Số lượng tồn kho -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Mua nhanh chỉ còn:</h3>
                        <span class="text-red-500 font-bold italic">
                            @if ($countfilter==2)
                                {{ $product->variants->where('color', $selectedColor)->where('size', $selectedSize)->sum('stock') }}
                            @else 
                                {{ $product->variants->where('size', $selectedSize)->sum('stock') }}
                            @endif
                            sản phẩm
                        </span>
                    </div>


                    <!-- Nút Thêm vào giỏ hàng -->
                    <div class="flex space-x-4 mb-6
                        @php
                            if ($countfilter == 1 and $selectedSize==[]){
                                echo "hidden";
                            } else if ($countfilter == 2 and ($selectedSize=='' or $selectedColor=='')){
                                echo "hidden";
                            }
                        @endphp
                        ">
                        @include('partials.button_add_cart')
                        <button
                            class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                            Mua ngay
                        </button>
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
