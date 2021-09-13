<?php

defined('SYSPATH') or die('No direct access allowed.');

return [
    [
        'name' => 'administrator.documentation.icon',
        'label' => 'Icon',
        'subnav' => [
            [
                'name' => 'administrator.documentation.icon.fa3',
                'label' => 'FontAwesome 3.2.1',
                'controller' => 'administrator/documentation/icon',
                'method' => 'fa3',
            ],
            [
                'name' => 'administrator.documentation.icon.fa4',
                'label' => 'FontAwesome 4.5.0',
                'controller' => 'administrator/documentation/icon',
                'method' => 'fa4',
            ],
            [
                'name' => 'administrator.documentation.icon.fa5',
                'label' => 'FontAwesome 5.0.13',
                'controller' => 'administrator/documentation/icon',
                'method' => 'fa5',
            ],
        ],
    ],
];
