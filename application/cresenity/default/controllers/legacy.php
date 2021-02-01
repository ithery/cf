<?php

/**
 * Description of legacy
 *
 * @author Hery
 */
class Controller_Legacy extends CController {
    public function __construct() {
        $app = CApp::instance();
        $app->setNav('legacy');
        $app->setTheme('cresenity-legacy');
        $app->setView('capp/legacy/page');
        $app->setViewLoginName('capp/legacy/login');
    }

    public function index() {
        $app = CApp::instance();

        $widget = $app->add_widget()->set_title('Dashboard');
        $widget->add('<h2>Welcome</h2>');
        $div = $widget->add_div();
        $div->add('<p>This is for testing purpose only when cresenity run on legacy framework (Cresenity 1.0 with old themes)</p>');
        $div->add('<p>This is theme now is deprecated and not supportable anymore, <strong>please do not use this template anymore</strong></p>');

        return $app;
    }

    public function element($method = null) {
        $app = CApp::instance();

        return $app;
    }
}
