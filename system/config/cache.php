<?php

defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Cache
 *
 * Cache settings, defined as arrays, or "groups". If no group name is
 * used when loading the cache library, the group named "default" will be used.
 *
 * Each group can be used independently, and multiple groups can be used at once.
 *
 * Group Options:
 *  driver   - Cache backend driver. Kohana comes with file, database, and memcache drivers.
 *              > File cache is fast and reliable, but requires many filesystem lookups.
 *              > Database cache can be used to cache items remotely, but is slower.
 *              > Memcache is very high performance, but prevents cache tags from being used.
 *
 *  params   - Driver parameters, specific to each driver.
 *
 *  lifetime - Default lifetime of caches in seconds. By default caches are stored for
 *             thirty minutes. Specific lifetime can also be set when creating a new cache.
 *             Setting this to 0 will never automatically delete caches.
 *
 *  requests - Average number of cache requests that will processed before all expired
 *             caches are deleted. This is commonly referred to as "garbage collection".
 *             Setting this to 0 or a negative number will disable automatic garbage collection.
 */
return array(
    'default' => 'file',
    'stores' => [
        'apc' => [
            'driver' => 'apc',
        ],
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
        ],
        'file' => [
            'driver' => 'file',
            'path' => DOCROOT . 'temp' . DS . 'cache',
        ],
        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => c::env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                c::env('MEMCACHED_USERNAME'),
                c::env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
            // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => c::env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => c::env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
        ],
        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => c::env('AWS_ACCESS_KEY_ID'),
            'secret' => c::env('AWS_SECRET_ACCESS_KEY'),
            'region' => c::env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => c::env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => c::env('DYNAMODB_ENDPOINT'),
        ],
    ],
);
