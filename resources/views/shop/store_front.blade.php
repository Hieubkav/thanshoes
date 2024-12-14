@php
    use App\Models\Product;
    $products = Product::all();

    // Lấy ra danh sách những thuộc tính khác nhau có thể có của product->brand trừ rỗng
    $brands = $products->pluck('brand')->filter()->unique();

    // lấy ra danh sách những bảng ghi khác nhau có thể có của product->type, lấy ra thì sort theo thứ tự từ nhiều đến ít của type đó
    $types = $products->pluck('type')->filter()->countBy()->sortDesc()->keys();

    // Lấy ra số tượng  của $types
    $count_types = $types->count();

@endphp

@extends('layouts.shoplayout')

@section('content')
    @include('component.carousel')

    @include('component.feature_benefit')

    @include('component.product_categories')

    @if ($count_types >= 1)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(0),
        ])
    @endif


    @include('component.discount_coupons')

    @if ($count_types >= 2)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(1),
        ])
    @endif

    @include('component.shop.cta')

    @if ($count_types >= 3)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(2),
        ])
    @endif

    @include('component.sport_active')

    @if ($count_types >= 4)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(3),
        ])
    @endif

    @include('component.banner_pic')

    @if ($count_types >= 5)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(4),
        ])
    @endif

    @include('component.carousel_slide')

    @if ($count_types >= 6)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(5),
        ])
    @endif

    @include('component.component_tech')

    @if ($count_types >= 7)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(6),
        ])
    @endif

    @include('component.video_banner')

    @if ($count_types >= 8)
        @include('component.new_arrival', [
            'type_name' => $types->values()->get(7),
        ])
    @endif

    @include('component.about_me')

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