@php
    $productId = $getProductId();
@endphp

@if ($productId)
    <livewire:product-image-manager :product-id="$productId" wire:key="image-manager-{{ $productId }}" />
@else 
    <div class="text-sm text-gray-500">
        Lưu sản phẩm trước khi thêm ảnh
    </div>
@endif
