<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 10, 2019, 7:17:48 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Cloud_Info extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        
        echo $app->render();
    }

}
