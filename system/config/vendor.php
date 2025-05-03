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
    'wago' => [
        'token' => c::env('WAGO_TOKEN'),
    ],
    'dropbox' => [

        /*
            * set the client id
            */
        'client_id' => c::env('DROPBOX_CLIENT_ID'),

        /*
            * set the client secret
            */
        'client_secret' => c::env('DROPBOX_SECRET_ID'),

        /*
            * Set the url to trigger the oauth process this url should call return Dropbox::connect();
            */
        'redirect_uri' => c::env('DROPBOX_OAUTH_URL'),

        /*
            * Set the url to redirecto once authenticated;
            */
        'landing_uri' => c::env('DROPBOX_LANDING_URL', '/'),

        /**
         * Set access token, when set will bypass the oauth2 process.
         */
        'access_token' => c::env('DROPBOX_ACCESS_TOKEN', ''),

        /**
         * Set access type, options are offline and online
         * Offline - will return a short-lived access_token and a long-lived refresh_token that can be used to request a new short-lived access token as long as a user's approval remains valid.
         *
         * Online - will return a short-lived access_token
         */
        'access_type' => c::env('DROPBOX_ACCESS_TYPE', 'offline'),

        /*
            set the scopes to be used
            */
        'scopes' => 'account_info.read files.metadata.write files.metadata.read files.content.write files.content.read',

    ]
];
