<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 14, 2018, 7:13:51 PM
 */
return [
    'cresenity' => [
        'css' => [
            'spinkit.css'
        ],
        'requirements' => ['block-ui'],
    ],
    'blockly' => [
        'js' => [
            'blockly/blockly_compressed.js',
            'blockly/blocks_compressed.js',
            'blockly/php_compressed.js',
            'blockly/msg/js/en.js',
            'element/blockly/blockly.js?' . uniqid(),
        ],
    ],
];
