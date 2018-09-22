<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 12:47:25 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use CApp_Administrator as Administrator;

class CApp_Administrator_Controller_User extends CApp_Administrator_Controller {

    public function __construct() {
        parent::__construct();
        $app = CApp::instance();

        if (!Administrator::isLogin()) {
            $app->setViewName('administrator/login');
        }

        CManager::instance()->theme()->setThemeCallback(function($theme) {
            return 'administrator';
        });


        CManager::instance()->navigation()->setNavigationCallback(function($navs) {
            $navFile = CF::getFile('data', 'Administrator/Navigation');
           
            return include($navFile);
        });
        
      
    }

}
