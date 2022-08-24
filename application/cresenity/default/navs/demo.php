<?php

return [
    [
        'name' => 'dashboard',
        'label' => c::__('Dashboard'),
        'uri' => 'demo/dashboard/index',
    ],
    [
        'name' => 'controls',
        'label' => c::__('Controls'),
        'uri' => 'demo/controls/index',
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
        'name' => 'cresjs',
        'label' => c::__('Cres JS'),
        'subnav' => include dirname(__FILE__) . '/demo/cresjs.php',
    ],
];
