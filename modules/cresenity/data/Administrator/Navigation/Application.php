<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 12:50:08 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
return array(
    array(
        "name" => "administrator.app.info",
        "label" => "Information",
        "controller" => "administrator/app/info",
        "method" => "index",
    ),
    array(
        "name" => "administrator.app.file",
        "label" => "File Manager",
        "controller" => "administrator/app/file",
        "method" => "index",
    ),
    
    array(
        "name" => "administrator.app.generator",
        "label" => "Generator",
        "controller" => "administrator/app/generator",
        "method" => "index",
    ),
    array(
        "name" => "administrator.app.fixer",
        "label" => "Fixer",
        "controller" => "administrator/app/fixer",
        "method" => "index",
    ),
    array(
        "name" => "administrator.app.cron",
        "label" => "Cron",
        "controller" => "administrator/app/cron",
        "method" => "index",
    ),
);
