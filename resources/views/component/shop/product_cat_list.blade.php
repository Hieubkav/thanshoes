<!-- Product List -->
<div id="product-list" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-2 md:gap-4">
    @foreach ($products as $item)
        <a href="{{route('shop.product_overview',$item->id)}}" class="border rounded-lg p-2 bg-white shadow hover:shadow-lg transition cursor-pointer">
            <!-- Thông tin sản phẩm -->
            <div class="relative group">
                <img 
                        src="
                        @php
                            $variant = $item->variants->first();
                            if ($variant && $variant->variant_images != null && $variant->variant_images->first()->image != "") {
                                echo $variant->variant_images->first()->image;
                            } else {
                                echo asset('images/logo.svg');
                            }
                        @endphp
                        " 
                        alt="Product 1" class="rounded-lg w-full cursor-pointer">

                @if ($item->variants->min('price') > 500000)
                    <span class="absolute top-1 left-1 bg-blue-600 text-white text-xs font-semibold px-1 py-1 rounded">FREESHIP</span>
                @endif
                <span class="absolute top-1 right-1 bg-red-500 text-white text-xs font-semibold px-1 py-1 rounded">-49%</span>
            </div>
            <div class="mt-4">
                <span class="text-xs text-gray-500 uppercase">{{ $item->brand }}</span>
                <h3 class="text-sm font-semibold text-gray-800">{{ $item->name }}</h3>
                <div class="flex items-center mt-2">
                    <p class="text-red-500 font-semibold">{{ number_format($item->variants->min('price'), 0, ',', '.') }}₫</p>
                    <p class="text-gray-500 line-through text-sm ml-2">{{ number_format(($item->variants->min('price') * 149) / 100, 0, ',', '.') }}₫</p>
                    
                </div>
            </div>
            <div class="flex items-center mt-3 space-x-2">
                @if ($item->variants->count() > 1)
                    <img src="{{ $item->variants->skip(1)->first()->variant_images->first()->image }}" alt="Color option 1" class="w-8 h-8 rounded-full border border-gray-300">
                @endif
                @if ($item->variants->count() > 2)
                    <img src="{{ $item->variants->skip(2)->first()->variant_images->first()->image }}" alt="Color option 2" class="w-8 h-8 rounded-full border border-gray-300">
                @endif
                <span class="text-gray-500 text-sm">{{ $item->variants->count() }}  phiên bản</span>
            </div>
        </a>
    @endforeach
</div>



