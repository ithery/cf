<?php

defined('SYSPATH') or die('No direct access allowed.');

return [
    /*
      |--------------------------------------------------------------------------
      | Default Filesystem Disk
      |--------------------------------------------------------------------------
      |
      | Here you may specify the default filesystem disk that should be used
      | by the framework. The "local" disk, as well as a variety of cloud
      | based disks are available to your application. Just store away!
      |
     */
    'default' => 'local',
    /*
      |--------------------------------------------------------------------------
      | Default Temporary Disk
      |--------------------------------------------------------------------------
      |
      | Here you may specify the default filesystem disk that should be used
      | by the temporary for the framework.
      |
     */
    'temp' => 'local-temp',
    /*
      |--------------------------------------------------------------------------
      | Default Public Temporary Disk
      |--------------------------------------------------------------------------
      |
      | Here you may specify the default filesystem disk that should be used
      | by the public temporary for the framework.
      |
     */
    'public-temp' => 'local-temp',
    /*
      |--------------------------------------------------------------------------
      | Default Cloud Filesystem Disk
      |--------------------------------------------------------------------------
      |
      | Many applications store files both locally and in the cloud. For this
      | reason, you may specify a default "cloud" driver here. This driver
      | will be bound as the Cloud disk implementation in the container.
      |
     */
    'cloud' => 's3',
    /*
      |--------------------------------------------------------------------------
      | Filesystem Disks
      |--------------------------------------------------------------------------
      |
      | Here you may configure as many filesystem "disks" as you wish, and you
      | may even configure multiple disks of the same driver. Defaults have
      | been setup for each driver as an example of the required options.
      |
      | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
      |
     */
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => DOCROOT,
        ],
        'local-files' => [
            'driver' => 'local',
            'root' => DOCROOT . 'files',
        ],
        'local-temp' => [
            'driver' => 'local',
            'root' => DOCROOT . 'temp',
            'url' => curl::httpbase() . 'temp',
            'visibility' => 'public',
        ],
        'public' => [
            'driver' => 'local',
            'root' => DOCROOT . 'public',
            'url' => curl::httpbase() . 'public',
            'visibility' => 'public',
        ],
        's3' => [
            'driver' => 's3',
            'key' => c::env('S3_AWS_KEY'),
            'secret' => c::env('S3_AWS_SECRET'),
            'region' => 'sgp1',
            'bucket' => 'resource',
            'endpoint' => c::env('S3_AWS_ENDPOINT'),
            'visibility' => 'public',
            'stream_reads' => true,
        ],
        's3-temp' => [
            'driver' => 's3',
            'key' => c::env('S3_AWS_KEY'),
            'secret' => c::env('S3_AWS_SECRET'),
            'region' => 'sgp1',
            'bucket' => 'temp-files',
            'endpoint' => c::env('S3_AWS_ENDPOINT'),
            'visibility' => 'public',
        ],
    ],
];
