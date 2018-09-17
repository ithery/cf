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
        "name" => "tools",
        "label" => "Tools",
        "icon" => "bolt",
        "subnav" => array(
            array(
                "name" => "console_menu",
                "label" => "Console",
                "subnav" => array(
                    array(
                        "name" => "shell_console",
                        "label" => "Shell Console",
                        "controller" => "shell_console",
                        "method" => "index",
                    ),
                ),
            ),
            array(
                "name" => "file_manager",
                "label" => "File Manager",
                "controller" => "file_manager",
                "method" => "index",
            ),
            array(
                "name" => "ajax_http_request",
                "label" => "Ajax HTTP Request",
                "controller" => "ajax_http_request",
                "method" => "index",
            ),
        ),
    ),
    
);
