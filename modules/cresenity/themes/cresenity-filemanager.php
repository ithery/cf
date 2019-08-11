<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 1:49:45 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

return array(
    "client_modules" => array(
        "jquery",
        "bootstrap-4",
        "fontawesome-5-f",
        "cropper",
        "dropzone",
     
    ),
    "js" => array(
        "capp.js?v=1",
        "cresenity.js",
    ),
    "css" => array(
        "cresenity.css",
        "cresenity/cresenity.bs4.css?v=" . uniqid(),
        "uikit.css",
        "spinkit.css",
        "cresenity/cresenity.main.bs4.css?v=" . uniqid(),
        "administrator/theme-material/theme-material.css?v=" . uniqid(),
    ),
   
    "data" => array(
       
    ),
);
