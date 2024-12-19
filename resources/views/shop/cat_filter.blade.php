@extends('layouts.shoplayout')

@section('content')
    {{-- @include('component.shop.cate_filter') --}}
    @livewire('product-filter')
@endsection