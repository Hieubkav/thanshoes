<!-- Modern Features & Benefits Section -->
@if($websiteDesign->service_status)
<section class="py-16 bg-gradient-to-b from-neutral-50 to-white">
    <div class="max-w-screen-xl mx-auto px-6">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-neutral-900 mb-4">Tại sao chọn ThanShoes?</h2>
            <p class="text-neutral-600 max-w-2xl mx-auto">Chúng tôi cam kết mang đến trải nghiệm mua sắm tuyệt vời nhất với những dịch vụ chất lượng cao</p>
        </div>

        <!-- Features Grid -->
        <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            @for($i = 1; $i <= 4; $i++)
            <div class="group bg-white rounded-xl p-6 text-center shadow-soft hover:shadow-soft-lg transition-all duration-300 border border-neutral-200/50 hover:border-primary-200">
                <!-- Icon Container -->
                <div class="relative mb-6">
                    <div class="w-20 h-20 mx-auto bg-primary-50 rounded-full flex items-center justify-center group-hover:bg-primary-100 transition-colors duration-300">
                        @if($websiteDesign->{"service_pic_" . $i})
                            <img src="{{config('app.asset_url')}}/storage/{{ $websiteDesign->{"service_pic_" . $i} }}"
                                 alt="Service icon {{ $i }}"
                                 loading="lazy"
                                 class="w-10 h-10 object-contain group-hover:scale-110 transition-transform duration-300">
                        @else
                            <!-- Default icon if no image -->
                            <i class="fas fa-star text-primary-500 text-2xl group-hover:scale-110 transition-transform duration-300"></i>
                        @endif
                    </div>
                    <!-- Decorative ring -->
                    <div class="absolute inset-0 w-20 h-20 mx-auto rounded-full border-2 border-primary-200 opacity-0 group-hover:opacity-100 group-hover:scale-125 transition-all duration-300"></div>
                </div>

                <!-- Content -->
                <div>
                    <h3 class="font-bold text-lg text-neutral-900 mb-3 group-hover:text-primary-600 transition-colors duration-300">
                        {{ $websiteDesign->{"service_title_" . $i} }}
                    </h3>
                    <p class="text-neutral-600 text-sm leading-relaxed">
                        {{ $websiteDesign->{"service_des_" . $i} }}
                    </p>
                </div>

                <!-- Hover indicator -->
                <div class="mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="w-12 h-1 bg-primary-500 rounded-full mx-auto"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>
@endif
