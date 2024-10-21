<?php

namespace Cresenity\Demo;

class Controller extends \CController {
    public function __construct() {
        parent::__construct();
        $app = \c::app();
        $app->setLoginRequired(false);
        $theme = \c::session()->get('theme', 'cresenity-demo');
        $app->setTheme($theme);
        $app->setView('demo');
        $app->setNav('demo');
    }
}
