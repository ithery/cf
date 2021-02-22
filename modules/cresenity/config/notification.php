<?php

return [
    'log_notification_model' => CNotification_Model_LogNotification::class,
    'queue' => [
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
];
