<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 13, 2019, 12:52:36 AM
 */
class Controller_Administrator_App_Fixer extends CApp_Administrator_Controller_User {
    public function index() {
        $app = CApp::instance();
        $app->title('Fixer');
        $tabList = $app->addTabList()->setAjax(true)->setTabPosition('top');
        $tab = $tabList->addTab()->setLabel('Database')->setAjaxUrl(curl::base() . 'administrator/app/fixer/database/index')->setNoPadding();
        $tab = $tabList->addTab()->setLabel('Table')->setAjaxUrl(curl::base() . 'administrator/app/fixer/table/index')->setNoPadding();

        echo $app->render();
    }
}
