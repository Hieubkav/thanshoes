<!-- Modern Features & Benefits Section -->
@if($websiteDesign->service_status)
<section class="py-4 bg-gradient-to-b from-neutral-50 to-white">
    <div class="max-w-screen-xl mx-auto px-3">
        <!-- Section Header -->
        <div class="text-center mb-3">
            <h2 class="text-xl font-bold text-neutral-900 mb-2">Tại sao chọn ThanShoes?</h2>
            <p class="text-neutral-600 text-sm max-w-xl mx-auto">Chúng tôi cam kết mang đến trải nghiệm mua sắm tuyệt vời nhất</p>
        </div>

        <!-- Features Grid -->
        <div class="grid gap-2 grid-cols-2 sm:grid-cols-2 lg:grid-cols-4">
            @for($i = 1; $i <= 4; $i++)
            <div class="group bg-white rounded-lg p-3 text-center shadow-sm hover:shadow-md transition-all duration-200 border border-neutral-100 hover:border-primary-200">
                <!-- Icon Container -->
                <div class="relative mb-2">
                    <div class="w-12 h-12 mx-auto rounded-full flex items-center justify-center  transition-colors duration-200">
                        @if($websiteDesign->{"service_pic_" . $i})
                            <img src="{{config('app.asset_url')}}/storage/{{ $websiteDesign->{"service_pic_" . $i} }}"
                                 alt="Service icon {{ $i }}"
                                 loading="lazy"
                                 class="w-6 h-6 object-contain group-hover:scale-110 transition-transform duration-200">
                        @else
                            <!-- Default icon if no image -->
                            <i class="fas fa-star text-primary-500 text-lg group-hover:scale-110 transition-transform duration-200"></i>
                        @endif
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <h3 class="font-bold text-sm text-neutral-900 mb-1 group-hover:text-primary-600 transition-colors duration-200">
                        {{ $websiteDesign->{"service_title_" . $i} }}
                    </h3>
                    <p class="text-neutral-600 text-xs leading-tight">
                        {{ $websiteDesign->{"service_des_" . $i} }}
                    </p>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>
@endif
