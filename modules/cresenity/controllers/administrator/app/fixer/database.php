<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 13, 2019, 12:54:46 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */


class Controller_Administrator_App_Fixer_Database extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();
        $app->title('Database Fixer');
        $tabList = $app->addTabList()->setAjax(true)->setTabPosition('left');
        $tab = $tabList->addTab()->setLabel('Relationship')->setAjaxUrl(curl::base() . 'administrator/app/fixer/database/relationship/index');
        $tab = $tabList->addTab()->setLabel('Data Type')->setAjaxUrl(curl::base() . 'administrator/app/fixer/database/datatype/index');

        echo $app->render();
    }

}
