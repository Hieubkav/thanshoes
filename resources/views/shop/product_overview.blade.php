@extends('layouts.shoplayout')

@section('content')
    @livewire('product-overview',compact('product'))
@endsection