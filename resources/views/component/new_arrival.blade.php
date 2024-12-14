@php
    $product = App\Models\Product::where('type', $type_name)->first();
    $so_luong_types = App\Models\Product::where('type', $type_name)->count();
    // Lấy ra tối đa 4 sản phẩm
    $danh_sach_types = App\Models\Product::where('type', $type_name)->take(4)->get();

@endphp
<div class="max-w-screen-xl mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold uppercase text-gray-700">
            {{ $type_name }}
            ( {{ $so_luong_types }} sản phẩm)
        </h2>
        <a href="#" class="text-sm text-blue-600 hover:underline">Xem tất cả</a>
    </div>

    <!-- Product List -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-2 md:gap-4">
        @foreach ($danh_sach_types as $item)
            <!-- Product Card 1 -->
            <div class="border rounded-lg p-2 bg-white shadow hover:shadow-lg transition">
                <div class="relative group">
                    <img src="{{ $item->variants->first()->variant_images->first()->image }}" alt="Product 1"
                        class="rounded-lg w-full">
                    <!-- Sale Badge -->
                    {{-- <span
                        class="absolute top-2 left-2 bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded">
                        Bán
                        chạy
                    </span> --}}
                    <!-- Discount Badge -->
                    @if ($item->variants->min('price') > 0)
                        <span
                            class="absolute top-2 right-2 bg-blue-600 text-white text-xs font-semibold px-2 py-1 rounded">
                            FREESHIP
                        </span>
                    @endif
                    <!-- Action Button -->
                    {{-- <button class="absolute bottom-2 right-2 bg-white p-2 rounded-full shadow hover:bg-gray-100">
                        <i class="fas fa-play text-gray-700"></i>
                    </button> --}}
                </div>
                <!-- Product Info -->
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
                <!-- Color Options -->
                <div class="flex items-center mt-3 space-x-2">
                    @if ($item->variants->count() > 1)
                        <img src="{{ $item->variants->skip(1)->first()->variant_images->first()->image }}" alt="Color option 1"
                            class="w-8 h-8 rounded-full border border-gray-300">
                    @endif
                    @if ($item->variants->count() > 2)
                        <img src="{{ $item->variants->skip(2)->first()->variant_images->first()->image }}" alt="Color option 2"
                            class="w-8 h-8 rounded-full border border-gray-300">
                    @endif
                    <span class="text-gray-500 text-sm">
                        {{ $item->variants->count() }} 
                        phiên bản
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>
