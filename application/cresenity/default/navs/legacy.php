<?php

/**
 * Description of nav.
 *
 * @author Hery
 */
return [
    [
        'name' => 'dashboard',
        'label' => clang::__('Dashboard'),
        'controller' => 'legacy',
        'method' => 'index',
        'icon' => 'home',
    ],
    [
        'name' => 'legacy.element',
        'label' => 'Element',
        'icon' => 'shopping-cart',
        'subnav' => include dirname(__FILE__) . '/legacy/element' . EXT,
    ],
];
