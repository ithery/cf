<?php

defined('SYSPATH') or die('No direct access allowed.');

return [
    'default' => [
        'benchmark' => true,
        'persistent' => false,
        'connection' => [
            'type' => 'mysqli',
            'user' => c::env('DB_USER'),
            'pass' => c::env('DB_PASS'),
            'host' => c::env('DB_HOST'),
            'port' => false,
            'socket' => false,
            'database' => c::env('DB_DATABASE')
        ],
        'character_set' => 'utf8mb4',
        'table_prefix' => '',
        'object' => true,
        'cache' => false,
        'escape' => true,
    ],

    'redis' => [
        'cluster' => false,
        'default' => [
            'host' => c::env('REDIS_DEFAULT_HOST'),
            'password' => c::env('REDIS_DEFAULT_PASSWORD'),
            'port' => 6379,
            'database' => 0,
        ],
    ],

];
