<?php

defined('SYSPATH') or die('No direct script access.');
/**
 * Database connection settings, defined as arrays, or "groups". If no group
 * name is used when loading the database library, the group named "default"
 * will be used.
 *
 * Each group can be connected to independently, and multiple groups can be
 * connected at once.
 *
 * Group Options:
 *  benchmark     - Enable or disable database benchmarking
 *  persistent    - Enable or disable a persistent connection
 *  connection    - Array of connection specific parameters; alternatively,
 *                  you can use a DSN though it is not as fast and certain
 *                  characters could create problems (like an '@' character
 *                  in a password):
 *                  'connection'    => 'mysql://dbuser:secret@localhost/kohana'
 *  character_set - Database character set
 *  table_prefix  - Database table prefix
 *  object        - Enable or disable object results
 *  cache         - Enable or disable query caching
 *  escape        - Enable automatic query builder escaping
 */
return [
    'default' => [
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
    ],
];
