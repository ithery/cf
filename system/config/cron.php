<?php

return [
    'cache' => [
        'store' => 'file'
    ],
    'logs' => [
        'rotation' => [
            'size' => 500 * 1024,
            'keep' => 10,
        ]
    ]
];
