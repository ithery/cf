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
];
