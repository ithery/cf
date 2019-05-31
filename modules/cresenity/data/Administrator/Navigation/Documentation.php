<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 15, 2019, 11:48:16 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
return array(
    array(
        "name" => 'administrator.documentation.icon',
        "label" => 'Icon',
        "subnav" => array(
            array(
                "name" => "administrator.documentation.icon.fa3",
                "label" => "FontAwesome 3.2.1",
                "controller" => 'administrator/documentation/icon',
                "method" => 'fa3',
            ),
            array(
                "name" => "administrator.documentation.icon.fa4",
                "label" => "FontAwesome 4.5.0",
                "controller" => 'administrator/documentation/icon',
                "method" => 'fa4',
            ),
            array(
                "name" => "administrator.documentation.icon.fa5",
                "label" => "FontAwesome 5.0.13",
                "controller" => 'administrator/documentation/icon',
                "method" => 'fa5',
            ),
        ),
    ),
);
