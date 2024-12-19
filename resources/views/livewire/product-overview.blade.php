<div>
    {{-- {{dd($list_sizes)}}} --}}
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
                            <h3 class="text-sm font-semibold text-blue-800 dark:text-white mb-2">Chọn phân loại:</h3>
                            <div class="flex space-x-4">
                                <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                    @foreach ($list_colors as $color)
                                        <label class="relative bg-blue-400 rounded-md border-none py-1 px-1 inline-block text-sm font-semibold uppercase cursor-pointer transform -skew-x-21 group">
                                            <input wire:model.live.debounce.300ms='selectedColor'
                                            @if ($product->variants->where('color',$color)->sum('stock')==0)
                                                disabled
                                            @endif
                                            value="{{ $color }}" type="radio" name="color" class="absolute opacity-0 w-0 h-0">
                                            <span class="inline-block transform skew-x-21">
                                                {{ $color }}
                                            </span>
                                            <div class="absolute top-0 bottom-0 left-0 right-0 bg-blue-900 opacity-0 -z-10 transition-all duration-500 group-hover:opacity-100 group-hover:left-0 group-hover:right-0"></div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Chọn kích cỡ -->
                    @if (count($list_sizes) > 0)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-2">Chọn phân loại:</h3>
                            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @foreach ($list_sizes as $size)
                                    <label class="relative  rounded-md border-none py-1 px-1 inline-block text-sm font-semibold uppercase  transform -skew-x-21 group
                                        @if ($product->variants->where('size',$size)->sum('stock')==0)
                                            bg-gray-400
                                            cursor-not-allowed
                                        @else
                                            bg-green-400
                                            cursor-pointer
                                        @endif
                                        ">
                                        <input wire:model.live.debounce.300ms='selectedSize' 
                                        @if ($product->variants->where('size',$size)->sum('stock')==0)
                                            disabled
                                        @endif
                                        value="{{ $size }}" type="radio" name="size" class="absolute opacity-0 w-0 h-0">
                                        <span class="inline-block transform skew-x-21">
                                            {{ $size }}
                                        </span>
                                        <div class="absolute top-0 bottom-0 left-0 right-0 bg-gray-900 opacity-0 -z-10 transition-all duration-500 group-hover:opacity-100 group-hover:left-0 group-hover:right-0"></div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Số lượng tồn kho -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Mua nhanh chỉ còn:</h3>
                        <span class="text-red-500 font-bold">6 sản phẩm</span>
                    </div>
                    

                    <!-- Nút Thêm vào giỏ hàng -->
                    <div class="flex space-x-4 mb-6">
                        {{-- <button 
                            class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500">
                            Thêm vào giỏ hàng
                        </button> --}}
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


    {{-- @include('partials.dispatch',[
        'event' => 'sizeSelected',
        'message' => 'cập nhậT  giá trj size thành  size'. $selectedSize,
        'color' => 'green'
    ]) --}}

</div>
