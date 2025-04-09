@foreach ($products as $item)
    <div class="border rounded-lg p-2 bg-white shadow hover:shadow-lg transition">
        <!-- Thông tin sản phẩm -->
        <div class="relative group">
            <img
                        src="
                        @php
                            $variant = $item->variants->first();
                            if ($variant && $variant->variantImage != null && $variant->variantImage->image != "") {
                                echo $variant->variantImage->image;
                            } else {
                                echo asset('images/logo.svg');
                            }
                        @endphp
                        "
                        alt="Product 1" class="rounded-lg w-full">

            @if ($item->variants->min('price') > 500000)
                <span
                    class="absolute top-2 right-2 bg-blue-600 text-white text-xs font-semibold px-2 py-1 rounded">FREESHIP</span>
            @endif
        </div>
        <div class="mt-4">
            <span class="text-xs text-gray-500 uppercase">{{ $item->brand }}</span>
            <h3 class="text-sm font-semibold text-gray-800">{{ $item->name }}</h3>
            <div class="flex items-center mt-2">
                <p class="text-red-500 font-semibold">
                    {{ number_format($item->variants->min('price'), 0, ',', '.') }}₫</p>
                <p class="text-gray-500 line-through text-sm ml-2">
                    {{ number_format(($item->variants->min('price') * 149) / 100, 0, ',', '.') }}₫</p>
                <span class="text-xs text-red-500 font-semibold ml-1">-49%</span>
            </div>
        </div>
        <div class="flex items-center mt-3 space-x-2">
            @if ($item->variants->count() > 1)
                <img src="{{ $item->variants->skip(1)->first()->variantImage->image }}"
                    alt="Color option 1" class="w-8 h-8 rounded-full border border-gray-300">
            @endif
            @if ($item->variants->count() > 2)
                <img src="{{ $item->variants->skip(2)->first()->variantImage->image }}"
                    alt="Color option 2" class="w-8 h-8 rounded-full border border-gray-300">
            @endif
            <span class="text-gray-500 text-sm">{{ $item->variants->count() }}  phiên bản</span>
        </div>
    </div>
@endforeach
