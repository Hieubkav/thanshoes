@extends('layouts.shoplayout')

@section('meta_description')
    {{ $product->seo_description ?? $product->description ?? config('app.name') . ' - ' . $product->name }}
@endsection

@section('og_title')
    {{ $product->name . ' | ' . config('app.name') }}
@endsection

@section('og_description')
    {{ $product->seo_description ?? $product->description ?? config('app.name') . ' - ' . $product->name }}
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
