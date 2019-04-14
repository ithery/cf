<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 14, 2019, 7:04:51 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Controller_Administrator_App_Info extends CApp_Administrator_Controller_User {

    public function index() {
        $app = CApp::instance();

        $app->setTitle('App Info');
        $tabList = $app->addTabList();
        $tabInfo = $tabList->addTab('info')->setLabel('Info')->setAjaxUrl(curl::base() . 'administrator/app/info/tabInfo');

        echo $app->render();
    }

    public function tabInfo() {
        $app = CApp::instance();
        $form = $app->addForm();
        $form->addField()->setLabel('App Code')->addControl('app_code', 'label')->setValue(CF::appCode());
        echo $app->render();
    }

}
