<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 12:02:00 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CApp_Administrator as Administrator;

class CApp_Administrator_Controller extends CController {

    public function __construct() {
        if (!isset($_COOKIE['capp-administrator'])) {
            CF::show404();
        }
        $manager = CManager::instance();
        $manager->theme()->setThemeCallback(function($theme) {
            return 'cresenity-administrator';
        });
        parent::__construct();
        $app = CApp::instance();
        $app->setLoginRequired(false);


        if (!Administrator::isEnabled()) {
            $app->setViewName('administrator/disabled');
        }

        $app->setViewName('administrator/page');
    }

}
