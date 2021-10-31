<?php

defined('SYSPATH') or die('No direct access allowed.');

return [
    [
        'name' => 'administrator.cloud.project.info',
        'label' => 'Project Information',
        'controller' => 'administrator/cloud/project/info',
        'method' => 'index',
    ],
    [
        'name' => 'administrator.cloud.info',
        'label' => 'Server Information',
        'controller' => 'administrator/cloud/server/info',
        'method' => 'index',
    ],
];
