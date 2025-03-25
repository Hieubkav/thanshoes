@props(['websiteDesign'])

@if($websiteDesign->about_status)
<section class="w-full py-12 bg-gradient-to-r from-gray-100 to-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8 animate-fade-in">
            {{ $websiteDesign->about_title }}
        </h2>
        <div class="max-w-3xl mx-auto flex flex-col lg:flex-row items-center gap-8">
            @if($websiteDesign->about_pic)
            <div class="lg:w-1/2">
                <img src="{{ asset('storage/' . $websiteDesign->about_pic) }}" 
                     alt="Về chúng tôi" 
                     class="w-full h-auto rounded-lg shadow-lg transition-transform duration-300 hover:scale-105">
            </div>
            @endif
            <div class="lg:w-1/2">
                <div class="bg-white shadow-lg rounded-lg p-6 transition-all duration-300 hover:shadow-xl">
                    <p class="text-gray-600 text-lg leading-relaxed">
                        {!! nl2br(e($websiteDesign->about_des)) !!}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<style>
    .animate-fade-in {
        animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
