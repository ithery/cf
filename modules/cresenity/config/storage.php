<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 3:42:24 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
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
        'local-temp' => [
            'driver' => 'local',
            'root' => DOCROOT.'temp',
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
            'key' => 'AMCFVMWR7P225JAAGFSL',
            'secret' => '04yvHWJJFrKv71VoGbqL3rZ6dcXjeJIReq85XqAi6jg',
            'region' => 'sgp1',
            'bucket' => 'resource',
            'endpoint' => 'https://sgp1.digitaloceanspaces.com',
        ],
        's3-temp' => [
            'driver' => 's3',
            'key' => 'AMCFVMWR7P225JAAGFSL',
            'secret' => '04yvHWJJFrKv71VoGbqL3rZ6dcXjeJIReq85XqAi6jg',
            'region' => 'sgp1',
            'bucket' => 'resource',
            'endpoint' => 'https://sgp1.digitaloceanspaces.com',
        ],
    ],
];
