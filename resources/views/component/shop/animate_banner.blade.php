@props(['websiteDesign'])

@if ($websiteDesign->image_banner_status)
<section class="overflow-hidden">
    <div class="max-w-5xl 2xl:max-w-6xl px-8 md:px-12 mx-auto py-2 lg:py-2 h-svh md:h-[50svh] space-y-2 flex flex-col justify-center">
        <div class="flex flex-col sm:flex-row mx-auto">
            @for ($i = 1; $i <= 4; $i++)
                @if ($websiteDesign->{"image_banner_link{$i}"})
                    <a href="#_">
                        <img src="{{ asset('storage/' . $websiteDesign->{"image_banner_link{$i}"}) }}"
                            class="rounded-xl {{ $i % 2 == 0 ? '-rotate-12' : 'rotate-6' }} hover:rotate-0 duration-500 hover:-translate-y-12 h-full w-full object-cover hover:scale-150 transform origin-bottom"
                            alt="Banner {{ $i }}">
                    </a>
                @endif
            @endfor
        </div>
    </div>
</section>
@endif
