<?php

return array(
    array(
        "name" => "dashboard",
        "label" => "Dashboard",
        "controller" => "home",
        "method" => "index",
        "icon" => "home",
    ),
    array(
        "name" => "get_started",
        "label" => "Get Started",
        "controller" => "get_started",
        "method" => "index",
        "icon" => "file",
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
                "name" => "app_list",
                "label" => "App",
                "controller" => "app",
                "method" => "index",
            ),
            array(
                "name" => "capp_list",
                "label" => "CApp Object",
                "subnav" => array(
                    array(
                        "name" => "database",
                        "label" => "Database",
                        "controller" => "database",
                        "method" => "index",
                    ),
                    array(
                        "name" => "table",
                        "label" => "Table",
                        "controller" => "table",
                        "method" => "index",
                    ),
                    array(
                        "name" => "form",
                        "label" => "Form",
                        "controller" => "form",
                        "method" => "index",
                    ),
                    array(
                        "name" => "widget",
                        "label" => "Widget",
                        "controller" => "widget",
                        "method" => "index",
                    ),
                    array(
                        "name" => "nestable",
                        "label" => "Nestable",
                        "controller" => "nestable",
                        "method" => "index",
                    ),
                    array(
                        "name" => "tab",
                        "label" => "Tab",
                        "controller" => "tab",
                        "method" => "index",
                    ),
                    array(
                        "name" => "action",
                        "label" => "Action",
                        "controller" => "action",
                        "method" => "index",
                    ),
                ),
            ),
            array(
                "name" => "manual",
                "label" => "Manual",
                "subnav" => array(
                    array(
                        "name" => "client_modules",
                        "label" => "Client Modules",
                        "controller" => "client_modules",
                        "method" => "index",
                    ),
                ),
            ),
        ),
    ),
    array(
        "name" => "library_menu",
        "label" => "Library",
        "icon" => "file",
        "subnav" => include dirname(__FILE__) . DS . "subnav" . DS . "library" . EXT,
    ),
);
