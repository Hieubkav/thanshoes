@php
$productImages = $this->getRecord()->productImages;
@endphp

@if(count($productImages) > 0)
    <div class="grid grid-cols-4 gap-4 mt-4">
        @foreach($productImages as $image)
            <div class="relative group">
                <img 
                    src="{{ $image->image_url }}" 
                    alt="Product image" 
                    class="rounded-lg shadow-md object-cover w-full h-32 border border-gray-200"
                />
                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 rounded-b-lg">
                    {{ $image->type === "variant" ? "Link URL" : "Upload" }} (#{{ $image->order }})
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-gray-500 text-sm">Chưa có hình ảnh cho sản phẩm này</div>
@endif
