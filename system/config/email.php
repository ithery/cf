<?php
defined('SYSPATH') or die('No direct access allowed.');

return [
    'default' => 'mail',
    'mailers' => [
        'sendgrid' => [
            'driver' => 'sendgrid',
        ],
        'mail' => [
            'driver' => 'mail'
        ],
    ],
    'from' => [
        'address' => 'noreply@capp.core',
        'name' => 'CF App',
    ],
];
