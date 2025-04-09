<?php

return [
    'default_filesystem_disk' => 'public',

    'layout' => [
        'sidebar' => [
            'is_collapsible_on_desktop' => true,
            'groups' => [],
            'items' => [],
        ],
        'footer' => [
            'should_show_logo' => true,
        ],
    ],

    'favicon' => null,

    'auth' => [
        'guard' => env('FILAMENT_AUTH_GUARD', 'web'),
        'pages' => [
            'login' => \Filament\Pages\Auth\Login::class,
        ],
    ],

    'pages' => [
        'namespace' => 'App\\Filament\\Pages',
        'path' => app_path('Filament/Pages'),
        'register' => [],
    ],

    'resources' => [
        'namespace' => 'App\\Filament\\Resources',
        'path' => app_path('Filament/Resources'),
        'register' => [],
    ],

    'widgets' => [
        'namespace' => 'App\\Filament\\Widgets',
        'path' => app_path('Filament/Widgets'),
        'register' => [],
    ],

    'notifications' => [
        'namespace' => 'App\\Filament\\Notifications',
        'path' => app_path('Filament/Notifications'),
        'register' => [],
    ],

    'upload_settings' => [
        'max_size' => 10240, // 10MB in kilobytes
        'accepted_file_types' => [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
        ],
    ],

    'support_address' => null,
    'sponsor_url' => null,
    'brand' => null,
];
