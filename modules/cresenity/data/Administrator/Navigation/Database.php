<?php

defined('SYSPATH') or die('No direct access allowed.');

return [
    [
        'name' => 'administrator.database.console',
        'label' => 'DB Console',
        'controller' => 'administrator/database/console',
        'method' => 'index',
    ],
    [
        'name' => 'administrator.database.generator',
        'label' => 'DB Generator',
        'controller' => 'administrator/database/generator',
        'method' => 'index',
    ],
    [
        'name' => 'administrator.database.tables',
        'label' => 'Tables',
        'controller' => 'administrator/database/tables',
        'method' => 'index',
    ],
];
