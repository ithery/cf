<?php
return [
    [
        'name' => 'elements.table',
        'label' => c::__('Table'),
        'subnav' => include dirname(__FILE__) . '/elements/table.php',
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
        'name' => 'elements.image',
        'label' => c::__('Image'),
        'uri' => 'demo/elements/image/index',
    ],
    [
        'name' => 'elements.repeater',
        'label' => c::__('Repeater') . ' [DEV]',
        'uri' => 'demo/elements/repeater/index',
    ],
    [
        'name' => 'elements.shimmer',
        'label' => c::__('Shimmer') . ' [DEV]',
        'uri' => 'demo/elements/shimmer/index',
    ],
    [
        'name' => 'elements.showMore',
        'label' => c::__('Show More'),
        'uri' => 'demo/elements/showMore/index',
    ],
    [
        'name' => 'elements.progressBar',
        'label' => c::__('Progress Bar'),
        'uri' => 'demo/elements/progressBar/index',
    ],
    [
        'name' => 'elements.gallery',
        'label' => c::__('Gallery'),
        'uri' => 'demo/elements/gallery/index',
    ],
    [
        'name' => 'elements.metric',
        'label' => c::__('Metric'),
        'subnav' => include dirname(__FILE__) . '/elements/metric.php',
    ],
];
