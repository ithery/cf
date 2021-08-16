<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 11, 2019, 1:49:45 AM
 */

return [
    'client_modules' => [
        'jquery',
        'bootstrap-4',
        'fontawesome-5-f',
        'jquery-ui-1.12.1.custom',
        'cropper',
        'dropzone',
    ],
    'js' => [
        'capp.js?v=2',
        'cresenity.js',
    ],
    'css' => [
        'cresenity.css',
        'cresenity/cresenity.bs4.css?v=' . uniqid(),
        'uikit.css',
        'spinkit.css',
        'cresenity/cresenity.main.bs4.css?v=' . uniqid(),
        'administrator/theme-material/theme-material.css?v=' . uniqid(),
    ],

    'data' => [
    ],
];
