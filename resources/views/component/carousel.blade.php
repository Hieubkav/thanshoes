@php
    $carousels = App\Models\Carousel::all();
@endphp

<!-- Modern Hero Carousel -->
<section class="relative w-full overflow-hidden">
    <div id="hero-carousel" class="relative w-full" data-carousel="slide">
        <!-- Carousel wrapper -->
        <div class="relative h-[12rem] md:h-[28rem] lg:h-[45rem] overflow-hidden rounded-b-2xl">
            @foreach($carousels as $key => $carousel)
            <div class="hidden duration-1000 ease-in-out h-full" data-carousel-item>
                <div class="relative w-full h-full">
                    <img src="{{config('app.asset_url')}}/storage/{{$carousel->link_image}}"
                         class="object-cover w-full h-full"
                         loading="lazy"
                         alt="Slide {{$key + 1}}">

                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>

                    <!-- Optional Content Overlay -->
                    <div class="absolute bottom-8 left-8 right-8 text-white">
                        <div class="max-w-2xl">
                            <!-- You can add dynamic content here if needed -->
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Modern Slider indicators -->
        <div class="absolute z-30 flex -translate-x-1/2 bottom-6 left-1/2 space-x-2">
            @foreach($carousels as $key => $carousel)
            <button type="button"
                    class="w-3 h-3 rounded-full transition-all duration-300 bg-white/50 hover:bg-white/80 data-[carousel-active]:bg-white data-[carousel-active]:w-8"
                    aria-current="{{$key === 0 ? 'true' : 'false'}}"
                    aria-label="Slide {{$key + 1}}"
                    data-carousel-slide-to="{{$key}}">
            </button>
            @endforeach
        </div>

        <!-- Modern Slider controls -->
        <button type="button"
                class="absolute top-1/2 left-4 z-30 -translate-y-1/2 group focus:outline-none"
                data-carousel-prev>
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 group-hover:bg-white/30 transition-all duration-300 shadow-lg">
                <i class="fas fa-chevron-left text-white text-lg"></i>
                <span class="sr-only">Previous</span>
            </span>
        </button>

        <button type="button"
                class="absolute top-1/2 right-4 z-30 -translate-y-1/2 group focus:outline-none"
                data-carousel-next>
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm border border-white/30 group-hover:bg-white/30 transition-all duration-300 shadow-lg">
                <i class="fas fa-chevron-right text-white text-lg"></i>
                <span class="sr-only">Next</span>
            </span>
        </button>
    </div>
</section>
