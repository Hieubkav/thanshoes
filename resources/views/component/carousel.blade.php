@php
    $carousels = App\Models\Carousel::all();
@endphp
<div id="default-carousel" class="relative w-full" data-carousel="slide">
    <!-- Carousel wrapper -->
    <div class="relative h-[10.5rem] md:h-[24.5rem] lg:h-[40rem] overflow-hidden">
        @foreach($carousels as $key => $carousel)
        <div class="hidden duration-700 ease-in-out" data-carousel-item>
            <img src="{{config('app.asset_url')}}/storage/{{$carousel->link_image}}" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="Slide {{$key + 1}}">
        </div>
        @endforeach
    </div>
    <!-- Slider indicators -->
    <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
        @foreach($carousels as $key => $carousel)
        <button type="button" class="w-3 h-3 rounded-full" aria-current="{{$key === 0 ? 'true' : 'false'}}" aria-label="Slide {{$key + 1}}" data-carousel-slide-to="{{$key}}"></button>
        @endforeach
    </div>
    <!-- Slider controls -->
    <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
            <i class="fas fa-chevron-left w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180"></i>
            <span class="sr-only">Previous</span>
        </span>
    </button>
    <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
            <i class="fas fa-chevron-right w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180"></i>
            <span class="sr-only">Next</span>
        </span>
    </button>
</div>
