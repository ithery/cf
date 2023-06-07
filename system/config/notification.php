<?php

return [
    'log_notification_model' => CNotification_Model_LogNotification::class,
    'queue' => [
        'queued' => false,
        'connection' => false,
        'name' => false,
    ],
    'task_queue' => [
        'notification_sender' => CNotification_TaskQueue_NotificationSender::class,
    ],
    'email' => [
        'vendor' => 'sendgrid',
    ],
    'sms' => [
        'vendor' => 'nexmo',
    ],
    'push_notification' => [
        'vendor' => 'firebase',
    ],
    'whatsapp' => [
        'vendor' => 'wago',
    ],
    'database' => [
        'model' => CModel_Notification_NotificationModel::class,
    ],
    'web' => [
        'enable' => false,
        'debug' => !CF::isProduction(),
        'start_url' => '/',
        'driver' => 'firebase',
        'options' => c::env('GOOGLE_FIREBASE_WEB_JS_CONFIG'),
        // 'groups' => [
        //     'admin' => [
        //         'enable' => true,
        //         'start_url' => '/admin/',
        //     ],
        //     'app' => [
        //         'enable' => true,
        //         'start_url' => '/app/',
        //     ],
        // ],
    ]
];
