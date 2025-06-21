@props(['websiteDesign'])

@if($websiteDesign->rep_like_real_status)
<div class="relative w-full h-auto md:h-80 lg:h-96 overflow-hidden max-w-screen mx-auto px-4 my-12">
    @if($websiteDesign->rep_like_real_pic)
    <img src="{{ asset('storage/' . $websiteDesign->rep_like_real_pic) }}"
         alt="Đánh giá từ khách hàng"
         loading="lazy"
         class="absolute inset-0 w-full h-full object-contain">
    <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center p-4 md:p-6 lg:p-8">
        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white text-center mb-4">ĐÁNH GIÁ KHÁCH HÀNG</h2>
        @if($websiteDesign->rep_like_real_link)
        <a href="{{ $websiteDesign->rep_like_real_link }}" class="bg-white text-black px-6 py-3 rounded-full font-semibold text-lg transition-all hover:bg-gray-200 hover:scale-105">
            XEM NGAY
        </a>
        @endif
    </div>
    @endif
</div>
@endif
