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
        'subnav' => [
            [
                'name' => 'elements.table',
                'label' => c::__('Table'),
                'uri' => 'demo/elements/table/index',
            ],
            [
                'name' => 'elements.widget',
                'label' => c::__('Widget'),
                'uri' => 'demo/elements/widget/index',
            ],
            [
                'name' => 'elements.tab',
                'label' => c::__('Tab'),
                'uri' => 'demo/elements/tab/index',
            ],
            [
                'name' => 'elements.form',
                'label' => c::__('Form'),
                'uri' => 'demo/elements/form/index',
            ],
            [
                'name' => 'elements.repeater',
                'label' => c::__('Repeater'),
                'uri' => 'demo/elements/repeater/index',
            ],
            [
                'name' => 'elements.shimmer',
                'label' => c::__('Shimmer'),
                'uri' => 'demo/elements/shimmer/index',
            ],
            [
                'name' => 'elements.showMore',
                'label' => c::__('Show More'),
                'uri' => 'demo/elements/showMore/index',
            ],
        ]
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
        ]
    ],

    [
        'name' => 'cresjs',
        'label' => c::__('Cres JS'),
        'subnav' => include dirname(__FILE__) . '/demo/cresjs.php',
    ],
];
