<?php

/**
 * Description of nav
 *
 * @author Hery
 */
return array(
    array(
        "name" => "dashboard",
        "label" => clang::__("Dashboard"),
        "controller" => "legacy",
        "method" => "index",
        "icon" => "home",
    ),
    array(
        "name" => "legacy.element",
        "label" => "Element",
        "icon" => "shopping-cart",
        "subnav" => include dirname(__FILE__) . "/legacy/element" . EXT,
    ),
);
