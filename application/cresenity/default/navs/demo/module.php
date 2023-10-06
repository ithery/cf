<?php
return [
    [
        'name' => 'module.formatter',
        'label' => c::__('Formatter'),
        'uri' => 'demo/module/formatter/index',
    ],
    [
        'name' => 'module.transform',
        'label' => c::__('Transform'),
        'uri' => 'demo/module/transform/index',
    ],
    [
        'name' => 'module.validation',
        'label' => c::__('Validation'),
        'uri' => 'demo/module/validation/index',
    ],
    [
        'name' => 'module.geo',
        'label' => c::__('Geo'),
        'uri' => 'demo/module/geo/index',
    ],
    [
        'name' => 'module.color',
        'label' => c::__('Color'),
        'uri' => 'demo/module/color/index',
    ],
    [
        'name' => 'module.broadcast',
        'label' => c::__('Broadcast'),
        'subnav' => include dirname(__FILE__) . '/module/broadcast.php',
    ],
    [
        'name' => 'module.ml',
        'label' => c::__('ML'),
        'uri' => 'demo/module/ml/index',
        'badge'=> 'DEV',
    ],
    [
        'name' => 'module.bot',
        'label' => c::__('Chat Bot'),
        'uri' => 'demo/module/bot/index',
    ],
];
