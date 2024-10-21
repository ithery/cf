<?php

defined('SYSPATH') or die('No direct access allowed.');

return [
    'client_modules' => [
    ],
    'js' => [
    ],
    'css' => [
    ],
    'data' => [
        'icon' => [
            'prefix' => 'icon icon-',
        ],
        'datatable' => [
            'dom' => null,
            'class' => null,
            'options' => [
                'deferRender' => true,
                'searching' => true,
                'processing' => true,
                'ordering' => true,
                'scrollX' => false,
                'serverSide' => false,
                'info' => true,
                'paging' => true,
                'searching' => true,
                'lengthChange' => true,
                'autoWidth' => false,
                'pagingType' => 'full_numbers',
                'height' => false,
                'stateSave' => false,
            ]
        ],
        'select2' => [
            'version' => null, //select2 version
        ],
        'radio' => [
            'js' => null, // js module for radio
        ],
        'tooltip' => [
            'icon' => 'fas fa-info-circle',
            'class' => '',
        ],
        'widget' => [
            'class' => [
                'wrapper' => null,
                'header' => null,
                'body' => null
            ]
        ],
        'range' => [
            'applyJs' => null, // ion-rangeslider
        ]
    ],
    'custom_js' => '',
];
