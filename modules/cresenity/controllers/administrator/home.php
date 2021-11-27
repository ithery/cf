<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2018, 12:02:55 AM
 */
class Controller_Administrator_Home extends CApp_Administrator_Controller_User {
    public function index() {
        $app = CApp::instance();
        $app->title('Dashboard');

        echo $app->render();
    }
}
