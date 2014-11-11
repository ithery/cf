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
        "name" => "contact_menu",
        "label" => "Contact",
        "icon" => "user",
        "subnav" => include dirname(__FILE__) . "/subnav/contact.php",
    ),
    array(
        "name" => "frontoffice_menu",
        "label" => "Front Office",
        "icon" => "building",
        "subnav" => include dirname(__FILE__) . "/subnav/frontoffice.php",
    ),
    array(
        "name" => "room_menu",
        "label" => "Room",
        "icon" => "gift",
        "subnav" => include dirname(__FILE__) . "/subnav/room.php",
    ),
    array(
        "name" => "setting_menu",
        "label" => "Setting",
        "icon" => "cog",
        "subnav" => include dirname(__FILE__) . "/subnav/setting.php",
    ),
);
