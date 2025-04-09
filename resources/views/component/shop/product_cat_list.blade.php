@php
    use App\Helpers\PriceHelper;
    use App\Models\Setting;
    $settings = Setting::first();
    $discountPercent = PriceHelper::getDiscountPercentage();
@endphp

<!-- Product List -->
<div id="product-list" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 gap-2 md:gap-4">
    @foreach ($products as $item)
        @php
            $variant = $item->variants->first();
            $image = asset('images/logo.svg'); // Default image

            if ($variant && $variant->variantImage && $variant->variantImage->image) {
                $image = $variant->variantImage->image;
            }
            
            // Calculate prices using the helper
            $minPrice = $item->variants->min('price');
            $discountedPrice = PriceHelper::calculateDiscountedPrice($minPrice);
            $originalPrice = PriceHelper::getDisplayOriginalPrice($minPrice);
        @endphp
        <a href="{{route('shop.product_overview',$item->id)}}" class="border rounded-lg p-2 bg-white shadow hover:shadow-lg transition cursor-pointer">
            <!-- Thông tin sản phẩm -->
            <div class="relative group">
                <img
                        src="{{ $image }}"
                        alt="Product 1" class="rounded-lg w-full cursor-pointer">

                @if ($item->variants->min('price') > 500000)
                    <span class="absolute top-1 left-1 bg-blue-600 text-white text-xs font-semibold px-1 py-1 rounded">FREESHIP</span>
                @endif
                @if($discountPercent > 0)
                <span class="absolute top-1 right-1 bg-red-500 text-white text-xs font-semibold px-1 py-1 rounded">-{{ $discountPercent }}%</span>
                @endif
            </div>
            <div class="mt-4">
                <span class="text-xs text-gray-500 uppercase">{{ $item->brand }}</span>
                <h3 class="text-sm font-semibold text-gray-800">{{ $item->name }}</h3>
                <div class="flex items-center mt-2">
                    <p class="text-red-500 font-semibold">{{ number_format($discountedPrice, 0, ',', '.') }}₫</p>
                    @if($discountPercent > 0)
                    <p class="text-gray-500 line-through text-sm ml-2">{{ number_format($originalPrice, 0, ',', '.') }}₫</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center mt-3 space-x-2">
                @if ($item->variants->count() > 1)
                    @php
                        $secondVariant = $item->variants->skip(1)->first();
                    @endphp
                    @if($secondVariant && $secondVariant->variantImage )
                        <img src="{{ $secondVariant->variantImage->image }}" alt="Color option 1" class="w-8 h-8 rounded-full border border-gray-300">
                    @endif
                @endif
                @if ($item->variants->count() > 2)
                    @php
                        $thirdVariant = $item->variants->skip(2)->first();
                    @endphp
                    @if($thirdVariant && $thirdVariant->variantImage)
                        <img src="{{ $thirdVariant->variantImage->image }}" alt="Color option 2" class="w-8 h-8 rounded-full border border-gray-300">
                    @endif
                @endif
                <span class="text-gray-500 text-sm">{{ $item->variants->count() }}  phiên bản</span>
            </div>
        </a>
    @endforeach
</div>



