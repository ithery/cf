<?php

defined('SYSPATH') or die('No direct access allowed.');

use CApp_Administrator as Administrator;

class CApp_Administrator_Controller_User extends CApp_Administrator_Controller {
    /**
     * Creates the controller, registering the datatable module and the
     * administrator navigation before delegating to the parent controller.
     */
    public function __construct() {
        $app = CApp::instance();
        if (!isset($_COOKIE['capp-administrator'])) {
            $app->setViewName('administrator/login');
        }
        CManager::registerModule('jquery.datatable', [
            'css' => ['administrator/datatables/datatables.css'],
            'js' => ['administrator/datatables/datatables.js'],
        ]);
        CManager::instance()->theme()->setThemeCallback(function ($theme) {
            return 'administrator';
        });

        parent::__construct();

        c::app()->setNav(function () {
            $navFile = CF::getFile('data', 'Administrator/Navigation');

            $navs = include $navFile;

            return $navs;
        });
    }
}
