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
        "icon" => " lnr lnr-home",
    ),
    array(
        "name" => "administrator.app",
        "label" => "Application",
        "icon" => " lnr lnr-dice",
        "subnav" => array(
            array(
                "name" => "administrator.app.info",
                "label" => "Information",
                "controller" => "administrator/app/info",
                "method" => "index",
            ),
        ),
    ),
    array(
        "name" => "administrator.database",
        "label" => "Database",
        "icon" => " lnr lnr-database",
        "subnav" => include dirname(__FILE__) . "/Navigation/Database" . EXT,
    ),
    array(
        "name" => "administrator.stats",
        "label" => "Stats",
        "icon" => " lnr lnr-chart-bars",
        "subnav" => array(
            array(
                "name" => "administrator.stats.phpinfo",
                "label" => "PHP Info",
                "controller" => "administrator/stats/phpinfo",
                "method" => "index",
            ),
        ),
    ),
    array(
        "name" => "administrator.vendor",
        "label" => "Vendor",
        "icon" => " lnr lnr-license",
        "subnav" => include dirname(__FILE__) . "/Navigation/Vendor" . EXT,
    ),
    array(
        "name" => "administrator.cloud",
        "label" => "Dev Cloud",
        "icon" => " lnr lnr-cloud",
        "subnav" => include dirname(__FILE__) . "/Navigation/Cloud" . EXT,
    ),
    array(
        "name" => "administrator.documentation",
        "label" => "Documentation",
        "icon" => " lnr lnr-question-circle",
        "subnav" => include dirname(__FILE__) . "/Navigation/Documentation" . EXT,
    ),
);
