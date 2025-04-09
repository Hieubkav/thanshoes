@php
    $images = $getRecord()->variant->variantImage;
@endphp

@if($images)
<div class="flex items-center p-1">
    <img
        src="{{ $images->image }}"
        alt="Ảnh sản phẩm"
        class="max-w-[30px] h-auto rounded shadow-sm"
    >
</div>
@endif
