<!-- Bắt đầu component thông tin lợi ích -->
@if($websiteDesign->service_status)
<div class="grid gap-3 grid-cols-2 lg:grid-cols-4 p-6 max-w-screen-xl mx-auto">
    @for($i = 1; $i <= 4; $i++)
    <div class="flex flex-col items-center p-1 border rounded-lg text-center hover:shadow-lg cursor-pointer">
        @if($websiteDesign->{"service_pic_" . $i})
            <img src="{{config('app.asset_url')}}/storage/{{ $websiteDesign->{"service_pic_" . $i} }}" alt="Service icon {{ $i }}" class="w-16 h-16 mb-3 object-contain">
        @endif
        <h3 class="font-semibold text-lg">{{ $websiteDesign->{"service_title_" . $i} }}</h3>
        <p class="text-gray-600">{{ $websiteDesign->{"service_des_" . $i} }}</p>
    </div>
    @endfor
</div>
@endif
<!-- Kết thúc component thông tin lợi ích -->
