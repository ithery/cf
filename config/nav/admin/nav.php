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
        "name" => "core_menu",
        "label" => "Core",
        "icon" => "bullseye",
        "subnav" => array(
            array(
                "name" => "app",
                "label" => "Application",
                "controller" => "app",
                "method" => "index",
            ),
            array(
                "name" => "organization",
                "label" => "Organization",
                "controller" => "organization",
                "method" => "index",
            ),
            array(
                "name" => "domain",
                "label" => "Domain",
                "controller" => "domain",
                "method" => "index",
            ),
        ),
    ),
    array(
        "name" => "temp",
        "label" => "Temporary",
        "controller" => "temp",
        "method" => "index",
        "icon" => "folder-close-alt",
    ),
);