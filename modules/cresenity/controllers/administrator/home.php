<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 12:02:55 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Home extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $app->title(clang::__("Dashboard"));


        echo $app->render();
    }

}
