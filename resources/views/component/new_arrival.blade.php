{{-- Dữ liệu đã được share từ ViewServiceProvider --}}
@php
    use App\Helpers\PriceHelper;
    use App\Models\Setting;
    $settings = Setting::first();
@endphp

@if($so_luong_types > 0)
<div class="max-w-screen-xl mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold uppercase text-gray-700">
            {{ $type_name }}
            ( {{ $so_luong_types }} sản phẩm)
        </h2>
        <a href="{{ route('shop.cat_filter',['type' => $type_name]) }}" class="text-sm text-blue-600 hover:underline">Xem tất cả</a>
    </div>

    <!-- Product List -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-2 md:gap-4">
        @foreach ($danh_sach_types ?? [] as $item)
            <!-- Product Card -->
            <a href="{{route('shop.product_overview',$item->slug)}}" class="border rounded-lg p-2 bg-white shadow hover:shadow-lg transition">
                <div class="relative group">
                    <!-- Product Image -->
                    @php
                        $firstVariant = $item->variants->first();
                        $image_variant = asset('images/logo.svg'); // Default image

                        if ($firstVariant && $firstVariant->variantImage && $firstVariant->variantImage->image) {
                            $image_variant = $firstVariant->variantImage->image;
                        }
                        
                        // Get minimum price from all variants
                        $minPrice = $item->variants->min('price');
                        
                        // Calculate the discounted price
                        $discountedPrice = PriceHelper::calculateDiscountedPrice($minPrice);
                        
                        // Calculate original/display price
                        $originalPrice = PriceHelper::getDisplayOriginalPrice($minPrice);
                        
                        // Calculate discount percentage
                        $discountPercent = PriceHelper::getDiscountPercentage();
                        
                        // Get discount type
                        $discountType = PriceHelper::getDiscountType();
                    @endphp

                    <img src="{{ $image_variant }}" loading="lazy" alt="Product Image" class="rounded-lg w-full">

                    <!-- Discount Badge -->
                    @if ($item->variants->min('price') > 500000)
                        <span class="absolute top-2 right-2 bg-blue-600 text-white text-xs font-semibold px-2 py-1 rounded">
                            FREESHIP
                        </span>
                    @endif
                </div>
                <!-- Product Info -->
                <div class="mt-4">
                    <span class="text-xs text-gray-500 uppercase">{{ $item->brand }}</span>
                    <h3 class="text-sm font-semibold text-gray-800">{{ $item->name }}</h3>
                    <div class="flex items-center mt-2">
                        <p class="text-red-500 font-semibold">
                            {{ number_format($discountedPrice, 0, ',', '.') }}₫</p>
                        @if($discountPercent > 0)
                            <p class="text-gray-500 line-through text-sm ml-2">
                                {{ number_format($originalPrice, 0, ',', '.') }}₫</p>
                            <span class="text-xs text-red-500 font-semibold ml-1">
                                @if($discountType == 'percent')
                                    -{{ $discountPercent }}%
                                @else
                                    -{{ number_format($discountPercent, 0, ',', '.') }}₫
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
                <!-- Color Options -->
                <div class="flex items-center mt-3 space-x-2">
                    @if ($item->variants->count() > 1 && $item->variants->skip(1)->first() && $item->variants->skip(1)->first()->variantImage && $item->variants->skip(1)->first()->variantImage->image)
                        <img src="{{ $item->variants->skip(1)->first()->variantImage->image }}" loading="lazy"
                            alt="Color option 1" class="w-8 h-8 rounded-full border border-gray-300">
                    @endif
                    @if ($item->variants->count() > 2 && $item->variants->skip(2)->first() && $item->variants->skip(2)->first()->variantImage && $item->variants->skip(2)->first()->variantImage->image)
                        <img src="{{ $item->variants->skip(2)->first()->variantImage->image }}" loading="lazy"
                            alt="Color option 2" class="w-8 h-8 rounded-full border border-gray-300">
                    @endif
                    <span class="text-gray-500 text-sm">
                        {{ $item->variants->count() }}
                        phiên bản
                    </span>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif