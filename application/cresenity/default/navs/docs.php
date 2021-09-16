<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Dec 5, 2020
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
            ],
            [
                'name' => 'basic.request',
                'label' => c::__('Request'),
                'uri' => 'docs/basic/request',
            ],
            [
                'name' => 'basic.view',
                'label' => c::__('View'),
                'uri' => 'docs/basic/view',
            ],
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
                'name' => 'command.introduction',
                'label' => c::__('Introduction'),
                'uri' => 'docs/command/introduction',
            ],
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
