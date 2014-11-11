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
        "name" => "my_account",
        "label" => "My Account",
        "icon" => "user",
        "subnav" => array(
            array(
                "name" => "my_task",
                "label" => "My Task",
                "controller" => "my_task",
                "method" => "index",
            ),
            array(
                "name" => "my_file",
                "label" => "My File",
                "controller" => "my_file",
                "method" => "index",
            ),
        ),
    ),
    array(
        "name" => "menu",
        "label" => "Menu",
        "icon" => "list",
        "subnav" => array(
            array(
                "name" => "project",
                "label" => "Project",
                "controller" => "project",
                "method" => "index",
            ),
            array(
                "name" => "project_task",
                "label" => "Project Task",
                "controller" => "project_task",
                "method" => "index",
            ),
            array(
                "name" => "project_module",
                "label" => "Project Module",
                "controller" => "project_module",
                "method" => "index",
            ),
        ),
    ),
    array(
        "name" => "kas",
        "label" => "Kas",
        "icon" => "book",
        "subnav" => array(
            array(
                "name" => "petty_cash",
                "label" => "Petty Cash",
                "controller" => "petty_cash",
                "method" => "index",
            ),
        ),
    ),
    array(
        "name" => "laporan",
        "label" => "Laporan",
        "icon" => "file-text-alt",
        "subnav" => array(
            array(
                "name" => "report_petty_cash",
                "label" => "Report Petty Cash",
                "controller" => "report_petty_cash",
                "method" => "index",
            ),
        ),
    ),
    array(
        "name" => "setting_list",
        "label" => "Setting",
        "icon" => "cog",
        "subnav" => include dirname(__FILE__) . DS . "subnav" . DS . "setting" . EXT,
//        "subnav" => include dirname(__FILE__) . "/subnav/setting.php",
    ), //end setting_list
);
