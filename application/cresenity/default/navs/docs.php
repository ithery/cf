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
        'subnav' => include dirname(__FILE__) . '/docs/starter.php',
    ],
    [
        'name' => 'basic',
        'label' => c::__('The Basic'),
        'subnav' => include dirname(__FILE__) . '/docs/basic.php',
    ],
    [
        'name' => 'app',
        'label' => c::__('Application'),
        'subnav' => include dirname(__FILE__) . '/docs/app.php',
    ],
    [
        'name' => 'element',
        'label' => c::__('Elements'),
        'subnav' => include dirname(__FILE__) . '/docs/element.php',
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
        'subnav' => include dirname(__FILE__) . '/docs/element.php',
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
                'name' => 'cresjs.confirm',
                'label' => c::__('Confirm'),
                'uri' => 'docs/cresjs/confirm',
            ],
            [
                'name' => 'cresjs.observer',
                'label' => c::__('Observer'),
                'uri' => 'docs/cresjs/observer',
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
            [
                'name' => 'cresjs.cssVar',
                'label' => c::__('CSS Var'),
                'uri' => 'docs/cresjs/cssVar',
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
                'name' => 'phpcf.model',
                'label' => c::__('Model'),
                'uri' => 'docs/phpcf/model',
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
