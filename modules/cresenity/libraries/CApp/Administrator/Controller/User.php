<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2018, 12:47:25 AM
 */
use CApp_Administrator as Administrator;

class CApp_Administrator_Controller_User extends CApp_Administrator_Controller {
    public function __construct() {
        $app = CApp::instance();
        if (!Administrator::isLogin()) {
            $app->setViewName('administrator/login');
        }

        CManager::instance()->theme()->setThemeCallback(function ($theme) {
            return 'administrator';
        });

        parent::__construct();

        CManager::instance()->navigation()->setNavigationCallback(function ($navs) {
            $navFile = CF::getFile('data', 'Administrator/Navigation');

            $navFile = include $navFile;
            $navAddition = CApp_Administrator::getNav();

            $navs = array_merge($navFile, $navAddition);

            return $navs;
        });
    }
}
