<?php

defined('SYSPATH') or die('No direct script access.');

return [
    'default' => 'mysqli',
    'connections' => [
        'mysqli' => [
            'benchmark' => false,
            'persistent' => false,
            'connection' => [
                'type' => 'mysqli',
                'user' => c::env('MYSQL_USER'),
                'pass' => c::env('MYSQL_PASSWORD'),
                'host' => c::env('MYSQL_HOST'),
                'port' => false,
                'socket' => false,
                'database' => c::env('MYSQL_DATABASE')
            ],
            'character_set' => 'utf8mb4',
            'table_prefix' => '',
            'object' => true,
            'cache' => false,
            'escape' => true,
        ]
    ],
    'redis' => [
        'client' => c::env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => c::env('REDIS_CLUSTER', 'redis'),
            'prefix' => c::env('REDIS_PREFIX', cstr::slug(c::env('APP_NAME', 'cf'), '_') . '_database_'),
        ],
        'cluster' => false,
        'supervisor' => [
            'host' => c::env('REDIS_SUPERVISOR_HOST'),
            'password' => c::env('REDIS_SUPERVISOR_PASSWORD'),
            'port' => 6379,
            'database' => 0,
        ],

    ],
];
