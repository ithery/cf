<?php

return [
    [
        'name' => 'dashboard',
        'label' => c::__('Dashboard'),
        'uri' => 'demo/dashboard/index',
    ],
    [
        'name' => 'app',
        'label' => c::__('App'),
        'subnav' => include dirname(__FILE__) . '/demo/app.php',
    ],
    [
        'name' => 'elements',
        'label' => c::__('Elements'),
        'subnav' => include dirname(__FILE__) . '/demo/elements.php',
    ],

    [
        'name' => 'controls',
        'label' => c::__('Controls'),
        'subnav' => include dirname(__FILE__) . '/demo/controls.php',
    ],
    [
        'name' => 'handler',
        'label' => c::__('Handler'),
        'subnav' => include dirname(__FILE__) . '/demo/handler.php',
    ],
    [
        'name' => 'model',
        'label' => c::__('Model'),
        'subnav' => include dirname(__FILE__) . '/demo/model.php',
    ],
    [
        'name' => 'cresjs',
        'label' => c::__('Cres JS'),
        'subnav' => include dirname(__FILE__) . '/demo/cresjs.php',
    ],
    [
        'name' => 'module',
        'label' => c::__('Module'),
        'subnav' => include dirname(__FILE__) . '/demo/module.php',
    ],
];
