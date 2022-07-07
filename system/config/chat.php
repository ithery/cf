<?php

return [
    'default' => 'default',
    'connections' => [
        'default' => [
            'user' => [
                'model' => CApp_Model_Users::class,
                'foreignKey' => null,
                'ownerKey' => null,
            ],

            'broadcast' => [
                'enable' => true,
                'app_name' => 'cres-chat',
                'driver' => c::env('CHAT_BROADCAST_DRIVER', 'pusher'), // pusher or laravel-websockets
                'pusher' => [
                    'app_id' => c::env('PUSHER_APP_ID', ''),
                    'app_key' => c::env('PUSHER_APP_KEY', ''),
                    'app_secret' => c::env('PUSHER_APP_SECRET', ''),
                    'options' => [
                        'cluster' => c::env('PUSHER_APP_CLUSTER', 'ap2'),
                        'encrypted' => c::env('PUSHER_APP_ENCRYPTION', false),
                        'host' => '127.0.0.1',
                        'port' => c::env('CHAT_WEBSOCKETS_PORT', 6001),
                        'scheme' => 'http',
                        'wsHost' => '127.0.0.1',
                        'wsPort' => c::env('CHAT_WEBSOCKETS_PORT', 6001),
                        'forceTLS' => false,
                        'disableStats' => true
                    ]
                ],
            ],

            'oembed' => [
                'enabled' => false,
                'url' => '',
                'key' => ''
            ]
        ]
    ]
];
