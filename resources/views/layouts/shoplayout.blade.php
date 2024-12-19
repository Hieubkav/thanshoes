<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        {{ env('APP_NAME') }}
    </title>

    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @livewireStyles
</head>

<body>
    

    @include('component.navbar')

    <main class="mt-[7.5rem]">
        @yield('content')
    </main>

    @include('component.bottom_navbar')

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

    @livewireScripts
</body>

</html>
