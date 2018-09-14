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
        "name" => "administrator.data",
        "label" => "Core",
        "icon" => "bullseye",
        "subnav" => array(
            array(
                "name" => "application",
                "label" => "Application",
                "controller" => "administrator/application",
                "method" => "index",
            ),
            array(
                "name" => "organization",
                "label" => "Organization",
                "controller" => "administrator/organization",
                "method" => "index",
            ),
        ),
    ),
    array(
        "name" => "data_menu",
        "label" => "Data",
        "icon" => "gift",
        "subnav" => array(
            array(
                "name" => "access",
                "label" => "Access",
                "subnav" => array(
                    array(
                        "name" => "roles",
                        "label" => "Roles",
                        "controller" => "roles",
                        "method" => "index",
                    ),
                    array(
                        "name" => "users",
                        "label" => "Users",
                        "controller" => "users",
                        "method" => "index",
                    ),
                    array(
                        "name" => "user_permission",
                        "label" => "User Permission",
                        "controller" => "user_permission",
                        "method" => "index",
                    ),
                ),
            ),
            array(
                "name" => "enterprise_menu",
                "label" => "Enterprise",
                "subnav" => array(
                    array(
                        "name" => "enterprise_item_stock_check",
                        "label" => "Item Stock Check",
                        "controller" => "enterprise_item_stock_check",
                        "method" => "index",
                    ),
                    array(
                        "name" => "enterprise_reset_data",
                        "label" => "Reset Data",
                        "controller" => "enterprise_reset_data",
                        "method" => "index",
                    ),
                ),
            ),
        ),
    ),
    array(
        "name" => "info_menu",
        "label" => "Info",
        "icon" => "info",
        "subnav" => array(
            array(
                "name" => "phpinfo",
                "label" => "PHP Info",
                "controller" => "phpinfo",
                "method" => "index",
            ),
            array(
                "name" => "server_variables",
                "label" => "Server Variables",
                "controller" => "server_variables",
                "method" => "index",
            ),
        ),
    ),
    array(
        "name" => "management",
        "label" => "Module",
        "icon" => "list",
        "subnav" => array(
            array(
                "name" => "geoip",
                "label" => "Geo IP",
                "controller" => "geoip",
                "method" => "index",
            ),
        ),
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
                "name" => "db_column_generator",
                "label" => "DB Column Generator",
                "controller" => "db_column_generator",
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
    array(
        "name" => "documentation_menu",
        "label" => "Documentation",
        "icon" => "question",
        "subnav" => array(
            array(
                "name" => "icon_list",
                "label" => "Supported Icon",
                "subnav" => array(
                    array(
                        "name" => "fontawesome_icon_list",
                        "label" => "Font-awesome",
                        "controller" => "fontawesome_icon_list",
                        "method" => "index",
                    )
                ),
            ),
            array(
                "name" => "api_list",
                "label" => "API",
                "subnav" => array(
                    array(
                        "name" => "api_example",
                        "label" => "Example",
                        "controller" => "api_example",
                        "method" => "index",
                    )
                ),
            ),
        ),
    ),
);
