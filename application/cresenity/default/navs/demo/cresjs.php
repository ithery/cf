<?php

return [
    [
        'name' => 'cresjs.alpine',
        'label' => c::__('Alpine'),
        'subnav' => include dirname(__FILE__) . '/cresjs/alpine.php',
    ],

    [
        'name' => 'cresjs.progressive',
        'label' => c::__('Progressive'),
        'uri' => 'demo/cresjs/progressive/index',
    ],
];
