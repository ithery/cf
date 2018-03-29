<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Documentation_Icon extends CController {

    public function index() {
        
    }

    public function fa3() {
        $app = CApp::instance();
        $app->title(clang::__("Fontawesome 3.2.1 Icons"));
        $app->addTemplate()->setTemplate('Documentation/Icon/FA3');
        echo $app->render();
    }

    public function fa4() {
        $app = CApp::instance();
        $app->title(clang::__("Fontawesome 4.5.0 Icons"));
        $app->addTemplate()->setTemplate('Documentation/Icon/FA4');
        echo $app->render();
    }

}

// End Home Controller