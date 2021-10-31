<?php

namespace Cresenity\Demo;

class Controller extends \CController {
    public function __construct() {
        parent::__construct();
        $app = \c::app();
        $app->setLoginRequired(false);
        $app->setTheme('cresenity-demo');
        $app->setView('demo');
        $app->setNav('demo');
    }
}
