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
        'subnav' => [
            [
                'name' => 'controls.text',
                'label' => c::__('Text'),
                'uri' => 'demo/controls/text/index',
            ],
            [
                'name' => 'controls.password',
                'label' => c::__('Password'),
                'uri' => 'demo/controls/password/index',
            ],
        ]
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
];
