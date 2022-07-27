<?php

return [
    'name' => 'CF PWA',
    'manifest' => [
        'name' => c::env('APP_NAME', 'CF PWA App'),
        'short_name' => 'PWA',
        'start_url' => '/',
        'background_color' => '#ffffff',
        'theme_color' => '#000000',
        'display' => 'standalone',
        'orientation' => 'any',
        'status_bar' => 'black',
        'icons' => [
            '72x72' => [
                'path' => '/system/media/img/pwa/icons/icon-72x72.png',
                'purpose' => 'any'
            ],
            '96x96' => [
                'path' => '/system/media/img/pwa/icons/icon-96x96.png',
                'purpose' => 'any'
            ],
            '128x128' => [
                'path' => '/system/media/img/pwa/icons/icon-128x128.png',
                'purpose' => 'any'
            ],
            '144x144' => [
                'path' => '/system/media/img/pwa/icons/icon-144x144.png',
                'purpose' => 'any'
            ],
            '152x152' => [
                'path' => '/system/media/img/pwa/icons/icon-152x152.png',
                'purpose' => 'any'
            ],
            '192x192' => [
                'path' => '/system/media/img/pwa/icons/icon-192x192.png',
                'purpose' => 'any'
            ],
            '384x384' => [
                'path' => '/system/media/img/pwa/icons/icon-384x384.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => '/system/media/img/pwa/icons/icon-512x512.png',
                'purpose' => 'any'
            ],
        ],
        'splash' => [
            '640x1136' => '/system/media/img/pwa/icons/splash-640x1136.png',
            '750x1334' => '/system/media/img/pwa/icons/splash-750x1334.png',
            '828x1792' => '/system/media/img/pwa/icons/splash-828x1792.png',
            '1125x2436' => '/system/media/img/pwa/icons/splash-1125x2436.png',
            '1242x2208' => '/system/media/img/pwa/icons/splash-1242x2208.png',
            '1242x2688' => '/system/media/img/pwa/icons/splash-1242x2688.png',
            '1536x2048' => '/system/media/img/pwa/icons/splash-1536x2048.png',
            '1668x2224' => '/system/media/img/pwa/icons/splash-1668x2224.png',
            '1668x2388' => '/system/media/img/pwa/icons/splash-1668x2388.png',
            '2048x2732' => '/system/media/img/pwa/icons/splash-2048x2732.png',
        ],
        'shortcuts' => [
            [
                'name' => 'Shortcut Link 1',
                'description' => 'Shortcut Link 1 Description',
                'url' => '/shortcutlink1',
                'icons' => [
                    'src' => '/system/media/img/pwa/icons/icon-72x72.png',
                    'purpose' => 'any'
                ]
            ],
            [
                'name' => 'Shortcut Link 2',
                'description' => 'Shortcut Link 2 Description',
                'url' => '/shortcutlink2'
            ]
        ],
        'custom' => []
    ]
];
