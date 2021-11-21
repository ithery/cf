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
        'name' => 'app',
        'label' => c::__('Application'),
        'subnav' => [
            [
                'name' => 'app.introduction',
                'label' => c::__('Introduction'),
                'uri' => 'docs/app/introduction',
            ],
            [
                'name' => 'app.setup',
                'label' => c::__('Setup'),
                'uri' => 'docs/app/setup',
            ],
            [
                'name' => 'app.theme',
                'label' => c::__('Theme'),
                'uri' => 'docs/app/theme',
            ],
            [
                'name' => 'app.element',
                'label' => c::__('Element'),
                'uri' => 'docs/app/element',
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
            ],
            [
                'name' => 'helper.cstr',
                'label' => c::__('cstr'),
                'uri' => 'docs/helper/cstr',
            ],
            [
                'name' => 'helper.curl',
                'label' => c::__('curl'),
                'uri' => 'docs/helper/curl',
            ]

        ]
    ],
    [
        'name' => 'module',
        'label' => c::__('Modules'),
        'subnav' => [
            [
                'name' => 'module.queue',
                'label' => c::__('Queue'),
                'uri' => 'docs/module/queue',
            ],
            [
                'name' => 'module.daemon',
                'label' => c::__('Daemon'),
                'uri' => 'docs/module/daemon',
            ],
        ]
    ],
    [
        'name' => 'cresjs',
        'label' => c::__('Cres JS'),
        'subnav' => [
            [
                'name' => 'cresjs.introduction',
                'label' => c::__('Introduction'),
                'uri' => 'docs/cresjs/introduction',
            ],
            [
                'name' => 'cresjs.basic',
                'label' => c::__('Basic'),
                'uri' => 'docs/cresjs/basic',
            ],
            [
                'name' => 'cresjs.php',
                'label' => c::__('PHPJS Function'),
                'uri' => 'docs/cresjs/php',
            ],
            [
                'name' => 'cresjs.reload',
                'label' => c::__('Reload'),
                'uri' => 'docs/cresjs/reload',
            ],
            [
                'name' => 'cresjs.ui',
                'label' => c::__('UI'),
                'uri' => 'docs/cresjs/ui',
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
                'name' => 'other.maintenance',
                'label' => c::__('Maintenance'),
                'uri' => 'docs/other/maintenance',
            ],
        ]
    ],
];
