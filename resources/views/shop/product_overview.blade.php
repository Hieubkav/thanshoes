@extends('layouts.shoplayout')

@php
    // Tạo SEO description tùy chỉnh cho sản phẩm
    function generateProductSeoDescription($product) {
        if ($product->seo_description) {
            return $product->seo_description;
        }

        $description = $product->name;
        if ($product->brand) {
            $description .= ' - ' . $product->brand;
        }
        if ($product->type) {
            $description .= ' (' . $product->type . ')';
        }

        // Thêm thông tin giá
        $minPrice = $product->variants->min('price');
        $maxPrice = $product->variants->max('price');
        if ($minPrice && $maxPrice) {
            if ($minPrice == $maxPrice) {
                $description .= ' - Giá: ' . number_format($minPrice, 0, ',', '.') . 'đ';
            } else {
                $description .= ' - Giá từ: ' . number_format($minPrice, 0, ',', '.') . 'đ';
            }
        }

        $description .= ' | ' . config('app.name') . ' - Chuyên giày thể thao chất lượng cao';
        return $description;
    }

    $productSeoDescription = generateProductSeoDescription($product);
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
