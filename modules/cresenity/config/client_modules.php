<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 14, 2018, 7:13:51 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
return array(
    "cresenity" => array(
        "css" => array(
            'spinkit.css'
        ),
        "requirements" => array("block-ui"),
    ),
    "blockly" => array(
        "js" => array(
            "blockly/blockly_compressed.js",
            "blockly/blocks_compressed.js",
            "blockly/php_compressed.js",
            "blockly/msg/js/en.js",
            "element/blockly/blockly.js?". uniqid(),
        ),
    ),
        
);
