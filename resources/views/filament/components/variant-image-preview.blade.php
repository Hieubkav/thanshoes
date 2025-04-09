@php
    $record = $getRecord();
    $imageUrl = null;
    
    if ($record && $record->variantImage && $record->variantImage->image_url) {
        $imageUrl = $record->variantImage->image_url;
    }
@endphp

@if($imageUrl)
<div class="flex flex-col items-center p-4">
    <div class="w-full max-w-md overflow-hidden rounded-lg border border-gray-200 shadow-sm">
        <img 
            src="{{ $imageUrl }}" 
            alt="Variant image preview" 
            class="h-auto w-full object-contain"
            style="max-height: 300px;"
        />
    </div>
    <div class="mt-2 text-sm text-gray-600">
        {{ $record->color ? 'Màu: ' . $record->color : '' }} 
        {{ $record->size ? ($record->color ? ' | ' : '') . 'Kích thước: ' . $record->size : '' }}
    </div>
</div>
@endif