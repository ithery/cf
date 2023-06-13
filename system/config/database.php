<?php

defined('SYSPATH') or die('No direct script access.');

return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            //'url' => c::env('MYSQL_URL'),
            'username' => c::env('MYSQL_USER'),
            'password' => c::env('MYSQL_PASSWORD'),
            'host' => c::env('MYSQL_HOST', '127.0.0.1'),
            'port' => c::env('MYSQL_PORT', 3306),
            'unix_socket' => c::env('MYSQL_SOCKET', ''),
            'database' => c::env('MYSQL_DATABASE'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'cache' => false,
            'benchmark' => c::env('MYSQL_BENCHMARK', !CF::isProduction()),
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            //'url' => c::env('PGSQL_URL'),
            'host' => c::env('PGSQL_HOST', '127.0.0.1'),
            'port' => c::env('PGSQL_PORT', '5432'),
            'database' => c::env('PGSQL_DATABASE'),
            'username' => c::env('PGSQL_USER'),
            'password' => c::env('PGSQL_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
            'benchmark' => c::env('PGSQL_BENCHMARK', !CF::isProduction()),

        ],
        'sqlsrv' => [
            'driver' => 'sqlsrv',
            //'url' => c::env('SQLSRV_URL'),
            'host' => c::env('SQLSRV_HOST', 'localhost'),
            'port' => c::env('SQLSRV_PORT', '1433'),
            'database' => c::env('SQLSRV_DATABASE', ''),
            'username' => c::env('SQLSRV_USERNAME', ''),
            'password' => c::env('SQLSRV_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'benchmark' => c::env('SQLSRV_BENCHMARK', !CF::isProduction()),
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => c::env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],
        'odbc' => [
            'driver' => 'odbc',
            'dsn' => c::env('ODBC_DSN'),
            'host' => c::env('ODBC_HOST'),
            'database' => c::env('ODBC_DB'),
            'username' => c::env('ODBC_USERNAME'),
            'password' => c::env('ODBC_PASSWORD'),
            'grammar' => [
                'query' => CDatabase_Query_Grammar_OdbcGrammar::class,
                'schema' => CDatabase_Schema_Grammar_OdbcGrammar::class,
            ]
        ],
    ],
    'redis' => [
        'client' => c::env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => c::env('REDIS_CLUSTER', 'redis'),
            'prefix' => c::env('REDIS_PREFIX', cstr::slug(c::env('APP_NAME', 'cf'), '_') . '_database_'),
        ],
        'default' => [
            'url' => c::env('REDIS_URL'),
            'host' => c::env('REDIS_HOST', '127.0.0.1'),
            'username' => c::env('REDIS_USERNAME'),
            'password' => c::env('REDIS_PASSWORD'),
            'port' => c::env('REDIS_PORT', '6379'),
            'database' => c::env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => c::env('REDIS_URL'),
            'host' => c::env('REDIS_HOST', '127.0.0.1'),
            'username' => c::env('REDIS_USERNAME'),
            'password' => c::env('REDIS_PASSWORD'),
            'port' => c::env('REDIS_PORT', '6379'),
            'database' => c::env('REDIS_CACHE_DB', '1'),
        ],

    ],
];
