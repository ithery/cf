<?php

defined('SYSPATH') or die('No direct access allowed.');

return [
    [
        'name' => 'administrator.setting.app',
        'label' => 'App',
        'controller' => 'administrator/setting/app',
        'method' => 'index',
        'action' => [
            [
                'name' => 'administrator.setting.app.edit',
                'label' => 'Edit',
                'controller' => 'administrator/setting/app',
                'method' => 'edit',
            ]
        ],
    ],
    [
        'name' => 'administrator.setting.database',
        'label' => 'Database',
        'controller' => 'administrator/setting/database',
        'method' => 'index',
        'action' => [
            [
                'name' => 'administrator.setting.database.edit',
                'label' => 'Edit',
                'controller' => 'administrator/setting/database',
                'method' => 'edit',
            ]
        ],
    ],
    [
        'name' => 'administrator.setting.cookie',
        'label' => 'Cookie',
        'controller' => 'administrator/setting/cookie',
        'method' => 'index',
        'action' => [
            [
                'name' => 'administrator.setting.cookie.edit',
                'label' => 'Edit',
                'controller' => 'administrator/setting/cookie',
                'method' => 'edit',
            ]
        ],
    ],
    [
        'name' => 'administrator.setting.cdn',
        'label' => 'CDN',
        'controller' => 'administrator/setting/cdn',
        'method' => 'index',
        'action' => [
            [
                'name' => 'administrator.setting.cdn.edit',
                'label' => 'Edit',
                'controller' => 'administrator/setting/cdn',
                'method' => 'edit',
            ]
        ],
    ],
];
