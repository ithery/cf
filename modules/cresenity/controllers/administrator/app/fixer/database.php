<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 13, 2019, 12:54:46 AM
 */
class Controller_Administrator_App_Fixer_Database extends CApp_Administrator_Controller_User {
    public function index() {
        $app = CApp::instance();
        $app->title('Database Fixer');
        $tabList = $app->addTabList()->setAjax(true)->setTabPosition('left');
        //$tab = $tabList->addTab()->setLabel('Table Engine')->setAjaxUrl(curl::base() . 'administrator/app/fixer/database/engine/index');
        $tab = $tabList->addTab()->setLabel('Column')->setAjaxUrl(curl::base() . 'administrator/app/fixer/database/column/index');
        $tab = $tabList->addTab()->setLabel('Data Type')->setAjaxUrl(curl::base() . 'administrator/app/fixer/database/datatype/index');
        $tab = $tabList->addTab()->setLabel('Relationship')->setAjaxUrl(curl::base() . 'administrator/app/fixer/database/relationship/index');
        $tab = $tabList->addTab()->setLabel('Collation')->setAjaxUrl(curl::base() . 'administrator/app/fixer/database/collation/index');

        echo $app->render();
    }
}
