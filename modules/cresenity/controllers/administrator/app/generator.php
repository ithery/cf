<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 12, 2019, 3:31:34 AM
 */
class Controller_Administrator_App_Generator extends CApp_Administrator_Controller_User {
    public function index() {
        $app = CApp::instance();
        $app->title('Generator');
        $tabList = $app->addTabList()->setAjax(true)->setTabPosition('top');
        $tab = $tabList->addTab()->setLabel('Model')->setAjaxUrl(curl::base() . 'administrator/app/generator/model/tables');

        echo $app->render();
    }
}
