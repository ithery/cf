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
                'name' => 'elements.Shimmer',
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
];
