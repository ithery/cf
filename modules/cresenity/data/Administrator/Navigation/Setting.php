<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 17, 2019, 1:55:26 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
return array(
    array(
        "name" => "administrator.setting.app",
        "label" => "App",
        "controller" => "administrator/setting/app",
        "method" => "index",
    ),
    array(
        "name" => "administrator.setting.cdn",
        "label" => "CDN",
        "controller" => "administrator/setting/cdn",
        "method" => "index",
    ),
);
