<?php
defined('SYSPATH') or die('No direct access allowed.');

return [
    'default' => c::env('MAIL_MAILER', 'mail'),
    'mailers' => [
        'mail' => [
            'transport' => 'mail',
        ],
        'smtp' => [
            'transport' => 'smtp',
            'url' => c::env('MAIL_URL'),
            'host' => c::env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => c::env('MAIL_PORT', 587),
            'encryption' => c::env('MAIL_ENCRYPTION', 'tls'),
            'username' => c::env('MAIL_USERNAME'),
            'password' => c::env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => c::env('MAIL_EHLO_DOMAIN'),
        ],
        'ses' => [
            'transport' => 'ses',
        ],

        'mailgun' => [
            'transport' => 'mailgun',
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => null,
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => c::env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => c::env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],
    ],
    'from' => [
        'address' => 'noreply@capp.core',
        'name' => 'CF App',
    ],
    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
    |
    */

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            DOCROOT . 'system/views/cresenity/email',
        ],
    ],
];
