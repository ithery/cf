<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 1:58:36 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Documentation_Control extends CController {

    public function index() {
        
    }

    public function text() {
        $app = CApp::instance();
        $app->title(clang::__("Input Text"));
        
        echo $app->render();
    }

}

// End Home Controller