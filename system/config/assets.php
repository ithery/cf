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
    ]
];
