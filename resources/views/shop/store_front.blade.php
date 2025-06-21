@php
    use App\Services\ProductCacheService;

    // Sử dụng ProductCacheService để tối ưu cache
    $products = ProductCacheService::getHomepageProducts();
    $websiteDesign = ProductCacheService::getWebsiteDesign();
    $hasPosts = ProductCacheService::hasPosts();
    $brands = ProductCacheService::getBrands();
    $typesData = ProductCacheService::getTypesData();

    $types = $typesData->keys();
    $count_types = $types->count();

@endphp

@extends('layouts.shoplayout')

@section('content')
    @include('component.carousel')

    @include('component.feature_benefit', ['websiteDesign' => $websiteDesign])

    @include('component.product_categories')

    @if ($count_types >= 1)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(0),
        ])
    @endif

    @include('component.shop.animate_banner', ['websiteDesign' => $websiteDesign])

    @if ($count_types >= 2)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(1),
        ])
    @endif

    @include('component.shop.cta', ['websiteDesign' => $websiteDesign])

    @if($hasPosts)
        @include('component.recent_posts')
    @endif

    @if ($count_types >= 3)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(2),
        ])
    @endif

    @include('component.sport_active', ['websiteDesign' => $websiteDesign])

    @if ($count_types >= 4)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(3),
        ])
    @endif

    @include('component.banner_pic', ['websiteDesign' => $websiteDesign])

    @if ($count_types >= 5)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(4),
        ])
    @endif

    @include('component.carousel_slide', ['websiteDesign' => $websiteDesign])

    @if ($count_types >= 6)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(5),
        ])
    @endif

    @include('component.component_tech', ['websiteDesign' => $websiteDesign])

    @if ($count_types >= 7)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(6),
        ])
    @endif

    @include('component.video_banner', ['websiteDesign' => $websiteDesign])

    @if ($count_types >= 8)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(7),
        ])
    @endif

    @include('component.about_me', ['websiteDesign' => $websiteDesign])

    @if ($count_types >= 9)
        @for ($i = 9; $i <= $count_types; $i++)
            @if ($products->where('type', $types->values()->get($i-1))->count() <= 3)
                @continue
                
            @endif

            @include('component.new_arrival', [
                'type_name' => $types->values()->get($i-1),
            ])
        @endfor
    @endif


@endsection
