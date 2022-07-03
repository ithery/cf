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
            ],
            [
                'name' => 'starter.vscode',
                'label' => c::__('VS Code Extension'),
                'uri' => 'docs/starter/vscode',
            ],
        ]
    ],
    [
        'name' => 'basic',
        'label' => c::__('The Basic'),
        'subnav' => [
            [
                'name' => 'basic.bootstrap',
                'label' => c::__('Bootstrap'),
                'uri' => 'docs/basic/bootstrap',
            ],
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
                'name' => 'app.navigation',
                'label' => c::__('Navigation'),
                'uri' => 'docs/app/navigation',
            ],
            [
                'name' => 'app.auth',
                'label' => c::__('Authentication'),
                'uri' => 'docs/app/auth',
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
            [
                'name' => 'app.other',
                'label' => c::__('Other'),
                'uri' => 'docs/app/other',
            ],
        ]
    ],
    [
        'name' => 'element',
        'label' => c::__('Elements'),
        'subnav' => [
            [
                'name' => 'element.standard',
                'label' => c::__('Standard Element'),
                'uri' => 'docs/element/standard',
            ],
            [
                'name' => 'element.table',
                'label' => c::__('Table'),
                'uri' => 'docs/element/table',
            ],
            [
                'name' => 'element.form',
                'label' => c::__('Form'),
                'uri' => 'docs/element/form',
            ],
            [
                'name' => 'element.tab',
                'label' => c::__('Tab'),
                'uri' => 'docs/element/tab',
            ],
            [
                'name' => 'element.widget',
                'label' => c::__('Widget'),
                'uri' => 'docs/element/widget',
            ],
            [
                'name' => 'element.showmore',
                'label' => c::__('ShowMore'),
                'uri' => 'docs/element/showmore',
            ],
            [
                'name' => 'element.shimmer',
                'label' => c::__('Shimmer'),
                'uri' => 'docs/element/shimmer',
            ],

        ]
    ],
    [
        'name' => 'forminput',
        'label' => c::__('Form Input'),
        'subnav' => [
            [
                'name' => 'forminput.standard',
                'label' => c::__('Standard Control'),
                'uri' => 'docs/forminput/standard',
            ],
            [
                'name' => 'forminput.selectsearch',
                'label' => c::__('Select Search'),
                'uri' => 'docs/forminput/selectsearch',
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
                'label' => c::__('CQueue'),
                'uri' => 'docs/module/queue',
            ],
            [
                'name' => 'module.daemon',
                'label' => c::__('CDaemon'),
                'uri' => 'docs/module/daemon',
            ],
            [
                'name' => 'module.cron',
                'label' => c::__('CCron'),
                'uri' => 'docs/module/cron',
            ],
            [
                'name' => 'module.period',
                'label' => c::__('CPeriod'),
                'uri' => 'docs/module/period',
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
                'name' => 'cresjs.css',
                'label' => c::__('Css'),
                'uri' => 'docs/cresjs/css',
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
            [
                'name' => 'cresjs.helper',
                'label' => c::__('Helper'),
                'uri' => 'docs/cresjs/helper',
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
        'name' => 'phpcf',
        'label' => c::__('PHP CF'),
        'subnav' => [
            [
                'name' => 'phpcf.install',
                'label' => c::__('Installation'),
                'uri' => 'docs/phpcf/install',
            ],
            [
                'name' => 'phpcf.testing',
                'label' => c::__('Testing'),
                'uri' => 'docs/phpcf/testing',
            ],
            [
                'name' => 'phpcf.make',
                'label' => c::__('Make'),
                'uri' => 'docs/phpcf/make',
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
