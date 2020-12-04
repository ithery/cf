<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Dec 5, 2020 
 * @license Ittron Global Teknologi
 */
return [
    [
        "name" => "starter",
        "label" => c::__("Getting Started"),
        "subnav" => [
            [
                "name" => "starter.installation",
                "label" => c::__("Installation"),
                "uri" => "docs/starter/installation",
            ],
            [
                "name" => "starter.configuration",
                "label" => c::__("Configuration"),
                "uri" => "docs/starter/configuration",
            ]
        ]
    ],
    [
        "name" => "basic",
        "label" => c::__("The Basic"),
        "subnav" => [
            [
                "name" => "basic.routing",
                "label" => c::__("Routing"),
                "uri" => "docs/basic/routing",
            ],
            [
                "name" => "basic.controller",
                "label" => c::__("Controller"),
                "uri" => "docs/basic/controller",
            ]
        ]
    ],
];
