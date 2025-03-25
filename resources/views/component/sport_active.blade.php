@props(['websiteDesign'])

@if($websiteDesign->effect_status)
<div class="container mx-auto py-8">
    <h2 class="text-2xl font-semibold mb-6 text-center">Giày ThanShoes có thể dùng để</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        @php
            $icons = [
                1 => ['icon' => 'fa-mitten', 'text' => 'BOXING'],
                2 => ['icon' => 'fa-person-running', 'text' => 'CHẠY BỘ'],
                3 => ['icon' => 'fa-dumbbell', 'text' => 'GYM'],
                4 => ['icon' => 'fa-umbrella-beach', 'text' => 'ĐI CHƠI'],
            ];
        @endphp

        @for($i = 1; $i <= 4; $i++)
            @if($websiteDesign->{"effect_pic_{$i}"})
                <div class="relative group overflow-hidden rounded-lg shadow-lg">
                    <img src="{{ asset('storage/' . $websiteDesign->{"effect_pic_{$i}"}) }}" 
                         alt="{{ $icons[$i]['text'] }}" 
                         class="w-full h-60 object-cover transition duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center">
                        <h3 class="text-white text-lg font-bold mb-2 flex items-center">
                            <i class="fa-solid {{ $icons[$i]['icon'] }} mr-2"></i> 
                            {{ $icons[$i]['text'] }}
                        </h3>
                    </div>
                </div>
            @endif
        @endfor
    </div>
</div>
@endif
