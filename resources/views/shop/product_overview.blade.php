@extends('layouts.shoplayout')

@php
    // Tạo SEO description tùy chỉnh cho sản phẩm
    $productSeoDescription = $product->seo_description;

    if (empty($productSeoDescription)) {
        $seoBase = $product->name;

        if ($product->brand) {
            $seoBase .= ' - ' . $product->brand;
        }

        if ($product->type) {
            $seoBase .= ' (' . $product->type . ')';
        }

        // Thêm thông tin giá
        $minPrice = $product->variants->min('price');
        $maxPrice = $product->variants->max('price');

        if ($minPrice && $maxPrice) {
            if ($minPrice == $maxPrice) {
                $seoBase .= ' - Giá: ' . number_format($minPrice, 0, ',', '.') . 'đ';
            } else {
                $seoBase .= ' - Giá từ: ' . number_format($minPrice, 0, ',', '.') . 'đ';
            }
        }

        $productSeoDescription = $seoBase . ' | ' . config('app.name') . ' - Chuyên giày thể thao chất lượng cao';
    }
@endphp

@section('meta_description')
    {{ $productSeoDescription }}
@endsection

@section('og_title')
    {{ $product->name . ' | ' . config('app.name') }}
@endsection

@section('og_description')
    {{ $productSeoDescription }}
@endsection

@section('og_image')
    @php
        $ogImage = null;
        if ($product->og_image) {
            $ogImage = asset('storage/' . $product->og_image);
        } elseif ($product->productImages->count() > 0) {
            $ogImage = $product->productImages->first()->image_url;
        } else {
            $ogImage = asset('images/og_img.webp');
        }
    @endphp
    {{ $ogImage }}
@endsection

@section('content')
    @livewire('product-overview',compact('product'))
@endsection

@push('styles')
<style>
    /* Add bottom padding to prevent content from being hidden behind mobile bottom actions */
    @media (max-width: 768px) {
        main[data-main-content] {
            padding-bottom: 120px !important;
        }
    }
</style>
@endpush
