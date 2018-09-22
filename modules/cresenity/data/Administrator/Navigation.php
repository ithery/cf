<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 1:01:04 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
return array(
    array(
        "name" => "administrator.dashboard",
        "label" => "Dashboard",
        "controller" => "administrator/home",
        "method" => "index",
        "icon" => "fas fa-home",
    ),
    
    array(
        "name" => "administrator.database",
        "label" => "Database",
        "icon" => " fas fa-database",
        "subnav" => array(
            array(
                "name" => "administrator.database.console",
                "label" => "DB Console",
                "controller" => "administrator/database/console",
                "method" => "index",
            ),
            array(
                "name" => "administrator.database.generator",
                "label" => "DB Generator",
                "controller" => "administrator/database/generator",
                "method" => "index",
            ),
        ),
    ),
    array(
        "name" => "administrator.stats",
        "label" => "Stats",
        "icon" => " fas fa-chart-bar",
        "subnav" => array(
            array(
                "name" => "administrator.stats.phpinfo",
                "label" => "PHP Info",
                "controller" => "administrator/stats/phpinfo",
                "method" => "index",
            ),
           
        ),
    ),
);
