<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 29, 2019, 6:57:16 PM
 * @see CElement_Component_Widget
 */
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
