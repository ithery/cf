<?php

return [
    'api_key' => c::env('DEVCLOUD_KEY'),
    'secret_key' => c::env('DEVCLOUD_SECRET'),
    'inspector' => [
        /*
        |--------------------------------------------------------------------------
        | Enabling
        |--------------------------------------------------------------------------
        |
        | Setting "false" the package stop sending data to Inspector.
        |
        */

        'enable' => c::env('DEVCLOUD_INSPECTOR_ENABLE', true),

        /*
        |--------------------------------------------------------------------------
        | Remote URL
        |--------------------------------------------------------------------------
        |
        | You can set the url of the remote endpoint to send data to.
        |
        */

        'url' => c::env('DEVCLOUD_INSPECTOR_URL', 'https://cpanel.ittron.co.id/inspector'),

        /*
        |--------------------------------------------------------------------------
        | Transport method
        |--------------------------------------------------------------------------
        |
        | This is where you can set the data transport method.
        | Supported options: "sync", "async"
        |
        */

        'transport' => 'async',

        /*
        |--------------------------------------------------------------------------
        | Max number of items.
        |--------------------------------------------------------------------------
        |
        | Max number of items to record in a single execution cycle.
        |
        */

        'max_items' => 100,

        /*
        |--------------------------------------------------------------------------
        | Proxy
        |--------------------------------------------------------------------------
        |
        | This is where you can set the transport option settings you'd like us to use when
        | communicating with Inspector.
        |
        */

        'options' => [
            // 'proxy' => 'https://55.88.22.11:3128',
            // 'curlPath' => '/usr/bin/curl',
        ],

        /*
        |--------------------------------------------------------------------------
        | Query
        |--------------------------------------------------------------------------
        |
        | Enable this if you'd like us to automatically add all queries executed in the timeline.
        |
        */

        'query' => true,

        /*
        |--------------------------------------------------------------------------
        | Bindings
        |--------------------------------------------------------------------------
        |
        | Enable this if you'd like us to include the query bindings.
        |
        */

        'bindings' => true,

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        |
        | Enable this if you'd like us to set the current user logged in via
        | Laravel's authentication system.
        |
        */

        'user' => true,

        /*
        |--------------------------------------------------------------------------
        | Email
        |--------------------------------------------------------------------------
        |
        | Enable this if you'd like us to monitor email sending.
        |
        */

        'email' => true,

        /*
        |--------------------------------------------------------------------------
        | Notifications
        |--------------------------------------------------------------------------
        |
        | Enable this if you'd like us to monitor notifications.
        |
        */

        'notifications' => true,

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        |
        | Enable this if you'd like us to monitor background job processing.
        |
        */

        'views' => true,

        /*
        |--------------------------------------------------------------------------
        | Job
        |--------------------------------------------------------------------------
        |
        | Enable this if you'd like us to monitor background job processing.
        |
        */

        'job' => true,

        /*
        |--------------------------------------------------------------------------
        | Job
        |--------------------------------------------------------------------------
        |
        | Enable this if you'd like us to monitor background job processing.
        |
        */

        'redis' => true,

        /*
        |--------------------------------------------------------------------------
        | Exceptions
        |--------------------------------------------------------------------------
        |
        | Enable this if you'd like us to report unhandled exceptions.
        |
        */

        'unhandled_exceptions' => true,

        /*
        |--------------------------------------------------------------------------
        | Http Client monitoring
        |--------------------------------------------------------------------------
        |
        | Enable this if you'd like us to report the http requests done using the CF HTTP Client.
        |
        */

        'http_client' => true,
        'http_client_body' => true,

        /*
        |--------------------------------------------------------------------------
        | Hide sensible data from http requests
        |--------------------------------------------------------------------------
        |
        | List request fields that you want mask from the http payload.
        | You can specify nested fields using the dot notation: "user.password"
        */

        'hidden_parameters' => [
            'password',
            'password_confirmation'
        ],

        /*
        |--------------------------------------------------------------------------
        | Artisan command to ignore
        |--------------------------------------------------------------------------
        |
        | Add at this list all command signature that you don't want monitoring
        | in your Inspector dashboard.
        |
        */

        'ignore_commands' => [
            'storage:link',
            'optimize',
            'optimize:clear',
            'schedule:run',
            'schedule:finish',
            'package:discover',
            'vendor:publish',
            'list',
            'test',
            'migrate',
            'migrate:rollback',
            'migrate:refresh',
            'migrate:fresh',
            'migrate:reset',
            'migrate:install',
            'cache:clear',
            'config:cache',
            'config:clear',
            'route:cache',
            'route:clear',
            'view:cache',
            'view:clear',
            'queue:listen',
            'queue:work',
            'queue:restart',
            'vapor:work',
            'horizon',
            'horizon:work',
            'horizon:supervisor',
            'horizon:terminate',
            'horizon:snapshot',
            'nova:publish',
        ],

        /*
        |--------------------------------------------------------------------------
        | Web request url to ignore
        |--------------------------------------------------------------------------
        |
        | Add at this list the url schemes that you don't want monitoring
        | in your Inspector dashboard. You can also use wildcard expression (*).
        |
        */

        'ignore_url' => [
            'telescope*',
            'vendor/telescope*',
            'horizon*',
            'vendor/horizon*',
            'nova*'
        ],

        /*
        |--------------------------------------------------------------------------
        | Job classes to ignore
        |--------------------------------------------------------------------------
        |
        | Add at this list the job classes that you don't want monitoring
        | in your Inspector dashboard.
        |
        */

        'ignore_jobs' => [
            //\App\Jobs\MyJob::class
        ],
    ]
];
