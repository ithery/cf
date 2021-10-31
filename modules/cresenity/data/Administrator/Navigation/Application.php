<?php

defined('SYSPATH') or die('No direct access allowed.');

return [
    [
        'name' => 'administrator.app.info',
        'label' => 'Information',
        'controller' => 'administrator/app/info',
        'method' => 'index',
    ],
    [
        'name' => 'administrator.app.file',
        'label' => 'File Manager',
        'controller' => 'administrator/app/file',
        'method' => 'index',
    ],

    [
        'name' => 'administrator.app.generator',
        'label' => 'Generator',
        'controller' => 'administrator/app/generator',
        'method' => 'index',
    ],
    [
        'name' => 'administrator.app.fixer',
        'label' => 'Fixer',
        'controller' => 'administrator/app/fixer',
        'method' => 'index',
    ],
    [
        'name' => 'administrator.app.daemon',
        'label' => 'Daemon',
        'controller' => 'administrator/app/daemon',
        'method' => 'index',
    ],
];
