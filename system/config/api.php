<?php

return [
    'default' => 'api',
    'groups' => [
        'api' => [
            'driver' => 'native',
            'session' => [
                'driver' => 'file',
                'expiration' => null,
            ],
        ],
    ],
];
