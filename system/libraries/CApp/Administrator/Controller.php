<?php

defined('SYSPATH') or die('No direct access allowed.');

use CApp_Administrator as Administrator;

class CApp_Administrator_Controller extends CController {
    /**
     * Creates the controller, guarding access behind the administrator
     * cookie and switching the active theme to the administrator theme.
     */
    public function __construct() {
        if (!isset($_COOKIE['capp-administrator'])) {
            CF::show404();
        }
        $manager = CManager::instance();
        $manager->theme()->setThemeCallback(function ($theme) {
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
