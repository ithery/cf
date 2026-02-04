<?php

defined('SYSPATH') or die('No direct access allowed.');

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
