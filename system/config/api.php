<?php

return [
    'default' => 'api',
    'groups' => [
        'api' => [
            'session' => [
                'driver' => 'file',
                'expiration' => null,
            ],
        ],
    ],
];
