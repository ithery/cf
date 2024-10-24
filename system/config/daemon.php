<?php

return [
    'supervisor' => [
        'enabled' => false,
        /*
        |--------------------------------------------------------------------------
        | Supervisor Domain
        |--------------------------------------------------------------------------
        |
        | This is the subdomain where Supervisor will be accessible from. If this
        | setting is null, Supervisor will reside under the same domain as the
        | application. Otherwise, this value will serve as the subdomain.
        |
        */

        'domain' => c::env('SUPERVISOR_DOMAIN'),

        /*
        |--------------------------------------------------------------------------
        | Supervisor Path
        |--------------------------------------------------------------------------
        |
        | This is the URI path where Supervisor will be accessible from. Feel free
        | to change this path to anything you like. Note that the URI will not
        | affect the paths of its internal API that aren't exposed to users.
        |
        */

        'path' => c::env('SUPERVISOR_PATH', 'supervisor'),

        /*
        |--------------------------------------------------------------------------
        | Supervisor Redis Connection
        |--------------------------------------------------------------------------
        |
        | This is the name of the Redis connection where Supervisor will store the
        | meta information required for it to function. It includes the list
        | of supervisors, failed jobs, job metrics, and other information.
        |
        */

        'use' => 'default',

        /*
        |--------------------------------------------------------------------------
        | Supervisor Redis Prefix
        |--------------------------------------------------------------------------
        |
        | This prefix will be used when storing all Supervisor data in Redis. You
        | may modify the prefix when you are running multiple installations
        | of Supervisor on the same server so that they don't have problems.
        |
        */

        'prefix' => c::env(
            'DAEMON_PREFIX',
            cstr::slug(CF::appCode(), '_') . '_supervisor:'
        ),

        /*
        |--------------------------------------------------------------------------
        | Supervisor Route Middleware
        |--------------------------------------------------------------------------
        |
        | These middleware will get attached onto each Supervisor route, giving you
        | the chance to add your own middleware to this list or change any of
        | the existing middleware. Or, you can simply stick with this list.
        |
        */

        'middleware' => ['web'],

        /*
        |--------------------------------------------------------------------------
        | Queue Wait Time Thresholds
        |--------------------------------------------------------------------------
        |
        | This option allows you to configure when the LongWaitDetected event
        | will be fired. Every connection / queue combination may have its
        | own, unique threshold (in seconds) before this event is fired.
        |
        */

        'waits' => [
            'redis:default' => 60,
        ],

        /*
        |--------------------------------------------------------------------------
        | Job Trimming Times
        |--------------------------------------------------------------------------
        |
        | Here you can configure for how long (in minutes) you desire Supervisor to
        | persist the recent and failed jobs. Typically, recent jobs are kept
        | for one hour while all failed jobs are stored for an entire week.
        |
        */

        'trim' => [
            'recent' => 60,
            'pending' => 60,
            'completed' => 60,
            'recent_failed' => 10080,
            'failed' => 10080,
            'monitored' => 10080,
        ],

        /*
        |--------------------------------------------------------------------------
        | Metrics
        |--------------------------------------------------------------------------
        |
        | Here you can configure how many snapshots should be kept to display in
        | the metrics graph. This will get used in combination with Supervisor's
        | `horizon:snapshot` schedule to define how long to retain metrics.
        |
        */

        'metrics' => [
            'cron' => [
                'enabled' => true,
                'expression' => '*/5 * * * *'
            ],
            'snapshot_lock' => 300,
            'trim_snapshots' => [
                'job' => 24,
                'queue' => 24,
            ],

        ],

        /*
        |--------------------------------------------------------------------------
        | Fast Termination
        |--------------------------------------------------------------------------
        |
        | When this option is enabled, Supervisor's "terminate" command will not
        | wait on all of the workers to terminate unless the --wait option
        | is provided. Fast termination can shorten deployment delay by
        | allowing a new instance of Supervisor to start while the last
        | instance will continue to terminate each of its workers.
        |
        */

        'fast_termination' => false,

        /*
        |--------------------------------------------------------------------------
        | Memory Limit (MB)
        |--------------------------------------------------------------------------
        |
        | This value describes the maximum amount of memory the Supervisor master
        | supervisor may consume before it is terminated and restarted. For
        | configuring these limits on your workers, see the next section.
        |
        */

        'memory_limit' => 64,

        /*
        |--------------------------------------------------------------------------
        | Queue Worker Configuration
        |--------------------------------------------------------------------------
        |
        | Here you may define the queue worker settings used by your application
        | in all environments. These supervisors and settings handle all your
        | queued jobs and will be provisioned by Supervisor during deployment.
        |
        */

        'defaults' => [
            'supervisor-default' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'maxProcesses' => 1,
                'maxTime' => 0,
                'maxJobs' => 0,
                'memory' => 128,
                'tries' => 1,
                'timeout' => 60,
                'nice' => 0,
            ],
        ],

        'environments' => [
            'production' => [
                'supervisor-default' => [
                    'maxProcesses' => 10,
                    'balanceMaxShift' => 1,
                    'balanceCooldown' => 3,
                ],
            ],

            'development' => [
                'supervisor-default' => [
                    'maxProcesses' => 3,
                ],
            ],
            'local' => [
                'supervisor-default' => [
                    'maxProcesses' => 3,
                ],
            ],
        ],
    ],
    'logs' => [
        'rotation' => [
            'size' => 500 * 1024,
            'keep' => 10,
        ]
    ]
];
