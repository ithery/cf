<?php
return [
    [
        'name' => 'elements.table',
        'label' => c::__('Table'),
        'subnav' => include dirname(__FILE__) . '/elements/table.php',
    ],
    [
        'name' => 'elements.listgroup',
        'label' => c::__('List Group'),
        'subnav' => include dirname(__FILE__) . '/elements/listgroup.php',
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
        'name' => 'elements.gallery',
        'label' => c::__('Gallery'),
        'uri' => 'demo/elements/gallery/index',
    ],
    [
        'name' => 'elements.chart',
        'label' => c::__('Chart'),
        'subnav' => include dirname(__FILE__) . '/elements/chart.php',
    ],
    [
        'name' => 'elements.metric',
        'label' => c::__('Metric'),
        'subnav' => include dirname(__FILE__) . '/elements/metric.php',
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
        'name' => 'elements.countDownTimer',
        'label' => c::__('Count Down Timer'),
        'uri' => 'demo/elements/countDownTimer/index',
    ],
    [
        'name' => 'elements.progressBar',
        'label' => c::__('Progress Bar'),
        'uri' => 'demo/elements/progressBar/index',
    ],
];
