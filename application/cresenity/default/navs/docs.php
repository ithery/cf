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
        'subnav' => include dirname(__FILE__) . '/docs/module.php',
    ],
    [
        'name' => 'cresjs',
        'label' => c::__('Cres JS'),
        'subnav' => include dirname(__FILE__) . '/docs/cresjs.php',
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
        'subnav' => include dirname(__FILE__) . '/docs/phpcf.php',
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
    [
        'name' => 'change',
        'label' => c::__('Changes Log'),
        'subnav' => include dirname(__FILE__) . '/docs/change.php',
    ],
];
