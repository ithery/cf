<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 *
 * @since Dec 5, 2020
 *
 * @license Ittron Global Teknologi
 */
return [
    [
        'name' => 'starter',
        'label' => c::__('Getting Started'),
        'subnav' => [
            [
                'name' => 'starter.installation',
                'label' => c::__('Installation'),
                'uri' => 'docs/starter/installation',
            ],
            [
                'name' => 'starter.configuration',
                'label' => c::__('Configuration'),
                'uri' => 'docs/starter/configuration',
            ]
        ]
    ],
    [
        'name' => 'basic',
        'label' => c::__('The Basic'),
        'subnav' => [
            [
                'name' => 'basic.routing',
                'label' => c::__('Routing'),
                'uri' => 'docs/basic/routing',
            ],
            [
                'name' => 'basic.controller',
                'label' => c::__('Controller'),
                'uri' => 'docs/basic/controller',
            ]
        ]
    ],
    [
        'name' => 'helper',
        'label' => c::__('Helpers'),
        'subnav' => [
            [
                'name' => 'helper.c',
                'label' => c::__('c'),
                'uri' => 'docs/helper/c',
            ],
            [
                'name' => 'helper.carr',
                'label' => c::__('carr'),
                'uri' => 'docs/helper/carr',
            ]
        ]
    ],
    [
        'name' => 'component',
        'label' => c::__('Components'),
        'subnav' => [
            [
                'name' => 'component.started',
                'label' => c::__('Get Started Component'),
                'uri' => 'docs/component/started',
            ],
        ]
    ],
    [
        'name' => 'command',
        'label' => c::__('Command'),
        'subnav' => [
            [
                'name' => 'command.basic',
                'label' => c::__('Basic'),
                'uri' => 'docs/command/basic',
            ],
        ]
    ],
    [
        'name' => 'other',
        'label' => c::__('Other'),
        'subnav' => [
            [
                'name' => 'other.basic',
                'label' => c::__('Basic'),
                'uri' => 'docs/other/basic',
            ],
        ]
    ],
];
