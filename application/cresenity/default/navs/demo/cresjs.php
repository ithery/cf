<?php

return [
    [
        'name' => 'cresjs.alpine',
        'label' => c::__('Alpine'),
        'subnav' => include dirname(__FILE__) . '/cresjs/alpine.php',
    ],
    [
        'name' => 'cresjs.react',
        'label' => c::__('React'),
        'subnav' => include dirname(__FILE__) . '/cresjs/react.php',
    ],

    [
        'name' => 'cresjs.progressive',
        'label' => c::__('Progressive'),
        'uri' => 'demo/cresjs/progressive/index',
    ],
    [
        'name' => 'cresjs.confirm',
        'label' => c::__('Confirm'),
        'uri' => 'demo/cresjs/confirm/index',
    ],

    [
        'name' => 'cresjs.reactive',
        'label' => c::__('Reactive'),
        'uri' => 'demo/cresjs/reactive/index',
    ],
];
