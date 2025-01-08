<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>

    <link rel="canonical" href="{{ request()->url() }}">
    <meta name="description"
          content="ThanShoes - Cửa hàng giày thể thao chính hãng với các thương hiệu nổi tiếng như New Balance, Nike, Onitsuka Tiger, Adidas, MLB, và Converse. Chúng tôi cung cấp đa dạng các mẫu giày chất lượng cao, đáp ứng mọi phong cách và nhu cầu, với giá cả cạnh tranh. Đến với ThanShoes để tìm đôi giày phù hợp nhất và nâng tầm phong cách của bạn.">
    <meta name="keywords"
          content="ThanShoes, giày thể thao, New Balance, Nike, Onitsuka Tiger, Adidas, MLB, Converse, giày chính hãng, cửa hàng giày thể thao, giày chất lượng cao, phong cách thể thao">
    <meta name="robots" content="all">
    <meta property="og:title" content="ThanShoes - Giày Thể Thao Chính Hãng, Nâng Tầm Phong Cách!">
    <meta property="og:description"
          content="ThanShoes.vn cung cấp đa dạng các mẫu giày thể thao chính hãng từ Nike, Adidas, New Balance, Onitsuka Tiger, MLB và Converse. Chất lượng đảm bảo, giá cả cạnh tranh, dịch vụ chăm sóc khách hàng tận tình. Nâng tầm phong cách của bạn với ThanShoes.vn."/>
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:image" content="{{asset('images/og_img.webp')}}">
    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "WebPage",
          "name": "ThanShoes.vn - Giày thể thao chính hãng cho giới trẻ",
          "description": "ThanShoes.vn là cửa hàng uy tín chuyên cung cấp giày thể thao chính hãng từ các thương hiệu nổi tiếng như Nike, Adidas, New Balance, Onitsuka Tiger, MLB và Converse. Chúng tôi cam kết mang đến sản phẩm chất lượng cao, phù hợp với phong cách năng động của giới trẻ.",
          "url": "https://thanshoes.vn"
        }
    </script>
    <meta name="revisit-after" content="1 day" />
    <meta name="HandheldFriendly" content="true">
    <meta http-equiv="x-dns-prefetch-control" content="on">
    <meta name="author" content="ThanShoes.vn">
    <meta http-equiv="refresh" content="300">


    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @filamentStyles
    @vite('resources/css/app.css')
</head>

<body class="antialiased">

@livewire('navbar')

<main class="mt-[7.5rem]">
    @yield('content')
</main>

{{--    @include('component.bottom_navbar')--}}

@include('component.footer')

@include('component.shop.speedial')


<script defer>
    document.addEventListener("DOMContentLoaded", function () {
        const allImages = document.querySelectorAll("img");

        allImages.forEach((img) => {
            if (!img.hasAttribute("loading")) {
                img.setAttribute("loading", "lazy");
            }
        });
        console.log("Đã thêm loading='lazy' vào tất cả ảnh.");
    });
</script>
@livewire('notifications')
@filamentScripts
@vite('resources/js/app.js')
</body>

</html>
