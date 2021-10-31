<?php

defined('SYSPATH') or die('No direct access allowed.');

return [
    [
        'name' => 'administrator.dashboard',
        'label' => 'Dashboard',
        'controller' => 'administrator/home',
        'method' => 'index',
        'icon' => ' lnr lnr-home',
    ],
    [
        'name' => 'administrator.app',
        'label' => 'Application',
        'icon' => ' lnr lnr-dice',
        'subnav' => include dirname(__FILE__) . '/Navigation/Application' . EXT,
    ],
    [
        'name' => 'administrator.qc',
        'label' => 'Quality Control',
        'icon' => ' lnr lnr-code',
        'subnav' => include dirname(__FILE__) . '/Navigation/QC' . EXT,
    ],
    [
        'name' => 'administrator.database',
        'label' => 'Database',
        'icon' => ' lnr lnr-database',
        'subnav' => include dirname(__FILE__) . '/Navigation/Database' . EXT,
    ],
    [
        'name' => 'administrator.stats',
        'label' => 'Stats',
        'icon' => ' lnr lnr-chart-bars',
        'subnav' => include dirname(__FILE__) . '/Navigation/Stats' . EXT,
    ],
    [
        'name' => 'administrator.vendor',
        'label' => 'Vendor',
        'icon' => ' lnr lnr-license',
        'subnav' => include dirname(__FILE__) . '/Navigation/Vendor' . EXT,
    ],
    [
        'name' => 'administrator.cdn',
        'label' => 'CDN',
        'icon' => ' lnr lnr-earth',
        'subnav' => include dirname(__FILE__) . '/Navigation/CDN' . EXT,
    ],
    [
        'name' => 'administrator.cloud',
        'label' => 'Dev Cloud',
        'icon' => ' lnr lnr-cloud',
        'subnav' => include dirname(__FILE__) . '/Navigation/Cloud' . EXT,
    ],
    [
        'name' => 'administrator.setting',
        'label' => 'Setting',
        'icon' => ' lnr lnr-cog',
        'subnav' => include dirname(__FILE__) . '/Navigation/Setting' . EXT,
    ],
    [
        'name' => 'administrator.documentation',
        'label' => 'Documentation',
        'icon' => ' lnr lnr-question-circle',
        'subnav' => include dirname(__FILE__) . '/Navigation/Documentation' . EXT,
    ],
];
