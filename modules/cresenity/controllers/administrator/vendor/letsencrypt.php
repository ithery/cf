<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 16, 2019, 12:10:44 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_Vendor_Letsencrypt extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $app->title('Lets Encrypt');
        echo $app->render();
    }

}
