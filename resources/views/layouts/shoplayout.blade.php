<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
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
        document.addEventListener("DOMContentLoaded", function() {
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
