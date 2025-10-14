<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>

    <link rel="canonical" href="{{ request()->url() }}">    <meta name="description"
          content="@yield('meta_description', optional($setting)->seo_description ?? 'ThanShoes - Cửa hàng giày thể thao chất lượng cao với các thương hiệu nổi tiếng như New Balance, Nike, Onitsuka Tiger, Adidas, MLB, và Converse. Chúng tôi cung cấp đa dạng các mẫu giày chất lượng cao, đáp ứng mọi phong cách và nhu cầu, với giá cả cạnh tranh. Đến với ThanShoes để tìm đôi giày phù hợp nhất và nâng tầm phong cách của bạn.')">
    <meta name="keywords"
          content="ThanShoes, giày thể thao, New Balance, Nike, Onitsuka Tiger, Adidas, MLB, Converse, giày chất lượng cao, cửa hàng giày thể thao, phong cách thể thao">
    <meta name="robots" content="all">    <meta property="og:title" content="@yield('og_title', 'ThanShoes - Giày Thể Thao Chất Lượng Cao, Nâng Tầm Phong Cách!')">    <meta property="og:description"
          content="@yield('og_description', optional($setting)->seo_description ?? 'ThanShoes.vn cung cấp đa dạng các mẫu giày thể thao chất lượng cao từ Nike, Adidas, New Balance, Onitsuka Tiger, MLB và Converse. Chất lượng đảm bảo, giá cả cạnh tranh, dịch vụ chăm sóc khách hàng tận tình. Nâng tầm phong cách của bạn với ThanShoes.vn.')"/>
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:image" content="@yield('og_image', optional($setting)->og_img ? asset('storage/' . optional($setting)->og_img) : asset('images/og_img.webp'))">
    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "WebPage",
          "name": "ThanShoes.vn - Giày thể thao chất lượng cao cho giới trẻ",
          "description": "ThanShoes.vn là cửa hàng uy tín chuyên cung cấp giày thể thao chất lượng cao từ các thương hiệu nổi tiếng như Nike, Adidas, New Balance, Onitsuka Tiger, MLB và Converse. Chúng tôi cam kết mang đến sản phẩm chất lượng cao, phù hợp với phong cách năng động của giới trẻ.",
          "url": "https://thanshoes.vn"
        }
    </script>
    <meta name="revisit-after" content="1 day" />
    <meta name="HandheldFriendly" content="true">
    <meta http-equiv="x-dns-prefetch-control" content="on">
    <meta name="author" content="ThanShoes.vn">
    <!-- link icon -->
    <link rel="icon" href="{{ asset('images/logo.svg') }}" type="image/x-icon">

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #FF6B35;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #E55722;
        }

        /* Flash message animations */
        @keyframes fade-in-down {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fade-in-down 0.5s ease-out;
        }

        .fi-notifications,
        .fi-notifications .fi-notification {
            z-index: 120 !important;
        }
    </style>

    @filamentStyles
    @vite('resources/css/app.css')
</head>

<body class="antialiased bg-neutral-50 text-neutral-900 font-sans">

@livewire('navbar')

<!-- Flash Messages -->
@if(session('success'))
    <div class="fixed top-24 right-4 z-[80] bg-green-500 text-white px-6 py-3 rounded-xl shadow-soft-lg border border-green-400 animate-fade-in-down">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div class="fixed top-24 right-4 z-[80] bg-red-500 text-white px-6 py-3 rounded-xl shadow-soft-lg border border-red-400 animate-fade-in-down">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    </div>
@endif

<main data-main-content class="pt-48 lg:pt-52 transition-all duration-300">
    @yield('content')
</main>

{{--    @include('component.bottom_navbar')--}}

@include('component.footer')

@include('component.shop.speedial')


<script defer>
    document.addEventListener("DOMContentLoaded", function () {
        const navbar = document.querySelector("[data-navbar]");
        const mainContent = document.querySelector("[data-main-content]");

        const updateMainOffset = () => {
            if (!navbar || !mainContent) {
                return;
            }

            const navRect = navbar.getBoundingClientRect();
            const buffer = window.innerWidth < 1024 ? 16 : 24;
            mainContent.style.paddingTop = `${Math.round(navRect.height + buffer)}px`;
        };

        updateMainOffset();
        window.addEventListener("resize", updateMainOffset);
        window.addEventListener("load", updateMainOffset);

        if (navbar) {
            const observer = new MutationObserver(updateMainOffset);
            observer.observe(navbar, { childList: true, subtree: true, attributes: true });
        }

        setTimeout(updateMainOffset, 300);
        setTimeout(updateMainOffset, 1000);

        const allImages = document.querySelectorAll("img");

        allImages.forEach((img) => {
            if (!img.hasAttribute("loading")) {
                img.setAttribute("loading", "lazy");
            }
        });
        console.log("Đã thêm loading='lazy' vào tất cả ảnh.");

        // Auto hide flash messages
        const flashMessages = document.querySelectorAll('.animate-fade-in-down');
        flashMessages.forEach(function(message) {
            setTimeout(function() {
                message.style.opacity = '0';
                message.style.transform = 'translateY(-20px)';
                setTimeout(function() {
                    message.remove();
                }, 300);
            }, 5000);
        });
    });

    // Handle logout event
    function handleLogout() {
        // Dispatch Livewire event
        if (window.Livewire) {
            window.Livewire.dispatch('user_logged_out');
        }
    }
</script>
@stack('modals')
@livewire('notifications')
@stack('scripts')
@filamentScripts
@vite('resources/js/app.js')
</body>

</html>

