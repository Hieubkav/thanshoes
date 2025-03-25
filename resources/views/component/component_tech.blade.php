@props(['websiteDesign'])

@if($websiteDesign->cer_status)
<div class="container mx-auto py-6 md:py-12 flex flex-col lg:flex-row items-center space-y-8 lg:space-y-0 lg:space-x-8 max-w-4xl">
    <!-- Text Content -->
    <div class="lg:w-1/2 text-center lg:text-left px-4">
        <h2 class="text-2xl md:text-3xl font-semibold mb-4">{{ $websiteDesign->cer_title }}</h2>
        <p class="text-gray-700 mb-6">
            {{ $websiteDesign->cer_des }}
        </p>
        @if($websiteDesign->cer_link)
        <a href="{{ $websiteDesign->cer_link }}" class="inline-block px-6 py-3 border border-gray-900 rounded-full text-gray-900 font-semibold hover:bg-gray-100">
            XEM NGAY
        </a>
        @endif
    </div>
    
    <!-- Image Content -->
    @if($websiteDesign->cer_image)
    <div class="lg:w-1/2 flex justify-center px-4">
        <img src="{{ asset('storage/' . $websiteDesign->cer_image) }}" 
             alt="{{ $websiteDesign->cer_title }}" 
             class="w-full max-w-md object-contain">
    </div>
    @endif
</div>
@endif
