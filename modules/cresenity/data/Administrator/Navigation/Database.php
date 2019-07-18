<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 16, 2019, 12:53:22 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
return array(
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
    array(
        "name" => "administrator.database.tables",
        "label" => "Tables",
        "controller" => "administrator/database/tables",
        "method" => "index",
    ),
);
