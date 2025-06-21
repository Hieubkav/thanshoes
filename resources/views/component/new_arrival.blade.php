{{-- Dữ liệu đã được share từ ViewServiceProvider với cache tối ưu --}}
@php
    use App\Helpers\PriceHelper;
    // Settings đã được cache trong ViewServiceProvider, sử dụng global $setting
@endphp

@if($so_luong_types > 0)
<section class="max-w-screen-xl mx-auto px-6 py-12">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-neutral-900 mb-2">
                {{ $type_name }}
            </h2>
            <p class="text-neutral-600 flex items-center">
                <i class="fas fa-box mr-2 text-primary-500"></i>
                {{ $so_luong_types }} sản phẩm có sẵn
            </p>
        </div>
        <a href="{{ route('shop.cat_filter',['type' => $type_name]) }}"
           class="btn btn-secondary group">
            Xem tất cả
            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform duration-200"></i>
        </a>
    </div>

    <!-- Product Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        @foreach ($danh_sach_types ?? [] as $item)
            <!-- Modern Product Card -->
            <a href="{{route('shop.product_overview',$item->slug)}}"
               class="group bg-white rounded-xl shadow-soft hover:shadow-soft-lg transition-all duration-300 overflow-hidden border border-neutral-200/50 hover:border-primary-200">
                <div class="relative overflow-hidden bg-neutral-50">
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

                    <img src="{{ $image_variant }}" loading="lazy" alt="{{ $item->name }}"
                         class="w-full h-48 sm:h-56 object-cover transition-transform duration-500 group-hover:scale-105">

                    <!-- Badges -->
                    <div class="absolute top-3 left-3 flex flex-col space-y-2">
                        @if ($discountPercent > 0)
                            <span class="chip chip-error text-xs font-semibold">
                                @if($discountType == 'percent')
                                    -{{ $discountPercent }}%
                                @else
                                    -{{ number_format($discountPercent, 0, ',', '.') }}₫
                                @endif
                            </span>
                        @endif
                        @if ($item->variants->min('price') > 500000)
                            <span class="chip chip-success text-xs font-semibold">
                                <i class="fas fa-shipping-fast mr-1"></i>
                                FREESHIP
                            </span>
                        @endif
                    </div>
                </div>
                <!-- Product Info -->
                <div class="p-4 sm:p-5">
                    <!-- Brand -->
                    <div class="mb-2">
                        <span class="text-xs font-medium uppercase tracking-wider text-neutral-500">{{ $item->brand }}</span>
                    </div>

                    <!-- Product Name -->
                    <h3 class="text-sm sm:text-base font-semibold text-neutral-900 mb-3 line-clamp-2 group-hover:text-primary-600 transition-colors duration-200">
                        {{ $item->name }}
                    </h3>

                    <!-- Price Section -->
                    <div class="flex items-center space-x-2 mb-4">
                        <span class="text-lg font-bold text-primary-600">
                            {{ number_format($discountedPrice, 0, ',', '.') }}₫
                        </span>
                        @if($discountPercent > 0)
                            <span class="text-sm text-neutral-500 line-through">
                                {{ number_format($originalPrice, 0, ',', '.') }}₫
                            </span>
                        @endif
                    </div>

                    <!-- Color Options & Variants -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="flex -space-x-1">
                                @if ($item->variants->count() > 1 && $item->variants->skip(1)->first() && $item->variants->skip(1)->first()->variantImage && $item->variants->skip(1)->first()->variantImage->image)
                                    <img src="{{ $item->variants->skip(1)->first()->variantImage->image }}" loading="lazy"
                                        alt="Color option 1" class="w-6 h-6 rounded-full border-2 border-white shadow-sm">
                                @endif
                                @if ($item->variants->count() > 2 && $item->variants->skip(2)->first() && $item->variants->skip(2)->first()->variantImage && $item->variants->skip(2)->first()->variantImage->image)
                                    <img src="{{ $item->variants->skip(2)->first()->variantImage->image }}" loading="lazy"
                                        alt="Color option 2" class="w-6 h-6 rounded-full border-2 border-white shadow-sm">
                                @endif
                            </div>
                            @if($item->variants->count() > 1)
                                <span class="text-xs text-neutral-500 font-medium">
                                    +{{ $item->variants->count() - 1 }} màu
                                </span>
                            @endif
                        </div>

                        <!-- Quick View Button -->
                        <button class="opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-x-2 group-hover:translate-x-0 text-primary-500 hover:text-primary-600">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif