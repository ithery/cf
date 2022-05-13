<?php

defined('SYSPATH') or die('No direct access allowed.');

return [
    'sendgrid' => [
        'key' => c::env('SENDGRID_API_KEY'),
    ],
    'google' => [
        'geocoding_api_key' => c::env('GEOCODING_API_KEY'),
        'recaptcha_v3_site_key' => c::env('RECAPTCHA_V3_SITE_KEY'),
        'recaptcha_v3_api_key' => c::env('RECAPTCHA_V3_API_KEY'),
        'recaptcha_v2_site_key' => c::env('RECAPTCHA_V2_SITE_KEY'),
        'recaptcha_v2_api_key' => c::env('RECAPTCHA_V2_API_KEY'),
    ],
    'zenziva' => [
        'key' => c::env('ZENZIVA_API_KEY'),
        'secret' => c::env('ZENZIVA_API_SECRET'),
    ],
    'onesignal' => [
        'user_key' => c::env('ONESIGNAL_USER_KEY'),
    ],
];
