@php
    use App\Helpers\PriceHelper;
    $discountType = PriceHelper::getDiscountType();
    $discountPercent = PriceHelper::getDiscountPercentage();
@endphp

<!-- Product List -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @foreach ($products as $product)
        @php
            // Lấy variant có giá nhỏ nhất
            $minPrice = $product->variants->min('price');
            $discountedPrice = PriceHelper::calculateDiscountedPrice($minPrice);
            $displayOriginalPrice = PriceHelper::getDisplayOriginalPrice($minPrice);
            
            // Lấy ảnh của variant đầu tiên
            $firstVariant = $product->variants->first();
            $firstImage = optional($firstVariant->variantImage)->image;
        @endphp
        <a href="{{ route('shop.product_overview', $product->slug) }}"
            class="group block bg-white rounded-lg shadow-md hover:shadow-lg overflow-hidden transition-all duration-300 transform hover:-translate-y-1">
            <div class="relative pt-[100%]">
                <img src="{{ $firstImage ?? asset('images/logo.svg') }}" alt="{{ $product->name }}"
                    class="absolute inset-0 w-full h-full object-cover">
                @if($discountPercent > 0)
                <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                    @if($discountType == 'percent')
                    -{{ $discountPercent }}%
                    @else
                    -{{ number_format($discountPercent, 0, ',', '.') }}₫
                    @endif
                </div>
                @endif
            </div>
            <div class="p-3">
                <div class="flex flex-col gap-2">
                    <span class="text-xs text-gray-500 uppercase">{{ $product->brand }}</span>
                    <h4 class="text-sm font-medium text-gray-800 line-clamp-2 min-h-[2.5rem] group-hover:text-blue-600 transition-colors">
                        {{ $product->name }}
                    </h4>
                    <div class="flex items-baseline gap-2">
                        <span class="text-base font-bold text-red-600">
                            {{ number_format($discountedPrice, 0, ',', '.') }}₫
                        </span>
                        @if($discountPercent > 0)
                        <span class="text-xs text-gray-500 line-through">
                            {{ number_format($displayOriginalPrice, 0, ',', '.') }}₫
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </a>
    @endforeach
</div>



