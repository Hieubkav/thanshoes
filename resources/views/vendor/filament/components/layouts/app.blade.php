<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament::layout.direction') ?? 'ltr' }}"
    @class([
        'dark' => filament()->hasDarkModeForced(),
    ])
>
    <head>
        {{ \Filament\Support\Facades\FilamentView::renderHook('filament::head.start') }}

        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        @foreach ($fonts as $family => $url)
            <link rel="preconnect" href="{{ $url }}" crossorigin />
            <link rel="preload" href="{{ $url }}" as="font" type="font/woff2" crossorigin />
        @endforeach

        {{ \Filament\Support\Facades\FilamentView::renderHook('filament::styles.before') }}

        <style>
            [x-cloak=''],
            [x-cloak='x-cloak'],
            [x-cloak='1'] {
                display: none !important;
            }

            @media (max-width: 1023px) {
                [x-cloak='-lg'] {
                    display: none !important;
                }
            }

            @media (min-width: 1024px) {
                [x-cloak='lg-'] {
                    display: none !important;
                }
            }
        </style>

        @filamentStyles

        {{ \Filament\Support\Facades\FilamentAsset::renderStyles() }}

        @stack('styles')

        <style>
            :root {
                --font-family: {!! join(', ', $fontFamilies) !!};
                --sidebar-width: {{ filament()->getSidebarWidth() }};
                --collapsed-sidebar-width: {{ filament()->getCollapsedSidebarWidth() }};
            }
        </style>

        {{ \Filament\Support\Facades\FilamentView::renderHook('filament::styles.after') }}

        <script>
            // Listen for refresh events from the image organizer window
            window.addEventListener('message', function(event) {
                if (event.data && event.data.type === 'refresh_product_images') {
                    console.log('[MESSAGE] Received refresh request from image organizer');
                    // Find and refresh the product images table
                    setTimeout(function() {
                        const refreshButton = document.querySelector('.filament-tables-refresh-button');
                        if (refreshButton) {
                            console.log('[REFRESH] Triggering refresh button click');
                            refreshButton.click();
                        } else {
                            console.log('[REFRESH] Reload page instead');
                            window.location.reload();
                        }
                    }, 500);
                }
            });
        </script>

        {{ \Filament\Support\Facades\FilamentView::renderHook('filament::head.end') }}
    </head>

    <body
        @class([
            'filament-app bg-gray-50 text-gray-950 dark:bg-gray-950 dark:text-white',
        ])
    >
        {{ \Filament\Support\Facades\FilamentView::renderHook('filament::body.start') }}

        <div x-data="{
            theme: null,

            init: function () {
                this.theme = localStorage.getItem('theme') || 'system'

                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
                    if (this.theme === 'system') {
                        window.location.reload()
                    }
                })

                $watch('theme', (theme) => {
                    if (theme === 'dark') {
                        document.documentElement.classList.add('dark')
                    } else if (theme === 'light') {
                        document.documentElement.classList.remove('dark')
                    } else {
                        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                            document.documentElement.classList.add('dark')
                        } else {
                            document.documentElement.classList.remove('dark')
                        }
                    }

                    localStorage.setItem('theme', theme)
                })
            }
        }" class="min-h-screen">
            {{ $slot }}
        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook('filament::scripts.before') }}

        @filamentScripts(withCore: true)

        {{ \Filament\Support\Facades\FilamentAsset::renderScripts() }}

        @stack('scripts')

        {{ \Filament\Support\Facades\FilamentView::renderHook('filament::scripts.after') }}

        {{ \Filament\Support\Facades\FilamentView::renderHook('filament::body.end') }}
    </body>
</html>