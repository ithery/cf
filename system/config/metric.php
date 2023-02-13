<?php

use InfluxDB2\Model\WritePrecision;

return [
    'default' => c::env('METRIC_CONNECTION'),

    'connection' => [
        'influxdb' => [
            'driver' => 'influxdb',
            'token' => c::env('INFLUXDB_TOKEN'),
            'url' => c::env('INFLUXDB_URL'),
            'bucket' => c::env('INFLUXDB_BUCKET'),
            'org' => c::env('INFLUXDB_ORG'),
            'precision' => WritePrecision::NS,
        ],
        'cloudwatch' => [
            'driver' => 'cloudwatch',
            'region' => c::env('CLOUDWATCH_REGION', c::env('AWS_DEFAULT_REGION', 'us-east-1')),
            'key' => c::env('AWS_ACCESS_KEY_ID'),
            'secret' => c::env('AWS_SECRET_ACCESS_KEY'),
            'namespace' => c::env('CLOUDWATCH_NAMESPACE')
        ]
    ],
];
