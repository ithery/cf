<?php
return [
    [
        'name' => 'listener.handler',
        'label' => c::__('Handler'),
        'subnav' => include dirname(__FILE__) . '/listener/handler.php',
    ],
    [
        'name' => 'listener.dependson',
        'label' => c::__('Depends On'),
        'uri' => 'demo/listener/dependson/index',
    ],
];
