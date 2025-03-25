@props(['websiteDesign'])

@if($websiteDesign->video_status && $websiteDesign->video_link)
<div class="w-full max-w-6xl mx-auto px-4 py-6">
    @php
        // Kiểm tra xem có phải link youtube không
        $isYoutubeLink = str_contains($websiteDesign->video_link, 'youtube.com') || 
                        str_contains($websiteDesign->video_link, 'youtu.be');
        
        // Lấy ID video từ link youtube
        if ($isYoutubeLink) {
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', 
                      $websiteDesign->video_link, 
                      $matches);
            $youtubeId = $matches[1] ?? null;
        }
    @endphp

    @if($isYoutubeLink && $youtubeId)
        <div class="relative pb-[56.25%] h-0">
            <iframe src="https://www.youtube.com/embed/{{ $youtubeId }}" 
                    class="absolute top-0 left-0 w-full h-full"
                    title="YouTube video"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen></iframe>
        </div>
    @else
        <video class="w-full" controls>
            <source src="{{ $websiteDesign->video_link }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    @endif
</div>
@endif
