<?php

defined('SYSPATH') or die('No direct access allowed.');

return [
    'css' => [
        'compile' => false,
        'disk' => 'local',
        'minify' => false,
        'filters' => [],
        'versioning' => false,
        'interval' => 0, // in minutes
    ],
    'js' => [
        'compile' => false,
        'minify' => false,
        'disk' => 'local',
        'filters' => [],
        'versioning' => false,
        'interval' => 0, // in minutes
    ],
    'scss' => [
        'source_map' => !CF::isProduction(),
    ],
    'google_fonts' => [
        'fonts' => [
            'default' => 'https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,700;1,400;1,700',
        ],
        'disk' => 'local',
        'path' => 'application/' . CF::appCode() . '/default/media/fonts/google_fonts',
        'inline' => true,
        'preload' => false,
        'fallback' => !c::env('APP_DEBUG'),
        'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Safari/605.1.15',
    ],
    'modules' => [

    ],

];
