<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 10, 2019, 7:20:19 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
return array(
    array(
        "name" => "administrator.cloud.project.info",
        "label" => "Project Information",
        "controller" => "administrator/cloud/project/info",
        "method" => "index",
    ),
    array(
        "name" => "administrator.cloud.info",
        "label" => "Server Information",
        "controller" => "administrator/cloud/server/info",
        "method" => "index",
    ),
);
