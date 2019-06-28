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
        $tabInfo = $tabList->addTab('navs')->setLabel('Nav')->setAjaxUrl(curl::base() . 'administrator/app/info/tabNav');

        echo $app->render();
    }

    public function tabInfo() {
        $app = CApp::instance();
        
        
        $tabList = $app->addTabList()->setTabPosition('top')->setAjax(false);
        $tabInfo = $tabList->addTab('infoCF')->setLabel('CF');
        $form = $tabInfo->addForm();
        $divRow = $form->addDiv()->addClass('row');
        
        $divRow->addDiv()->addClass('col-sm-12')->addField()->setLabel('Version')->addControl('version', 'label')->setValue(CF::version());
        $divRow->addDiv()->addClass('col-sm-12')->addField()->setLabel('Domain')->addControl('domain', 'label')->setValue(CF::domain());
        $divRow->addDiv()->addClass('col-sm-6')->addField()->setLabel('App ID')->addControl('app_id', 'label')->setValue(CF::appId());
        $divRow->addDiv()->addClass('col-sm-6')->addField()->setLabel('App Code')->addControl('app_code', 'label')->setValue(CF::appCode());
        $divRow->addDiv()->addClass('col-sm-6')->addField()->setLabel('Org ID')->addControl('org_id', 'label')->setValue(CF::orgId());
        $divRow->addDiv()->addClass('col-sm-6')->addField()->setLabel('Org Code')->addControl('org_code', 'label')->setValue(CF::orgCode());
        echo $app->render();
    }

    public function tabNav() {
        $app = CApp::instance();
        $form = $app->addForm();
        //remove the navigation callback
        $callback = CApp_Navigation_Data::getNavigationCallback();
        CApp_Navigation_Data::removeNavigationCallback();
        $navs = CApp_Navigation::navs(CF::domain());
        CApp_Navigation_Data::setNavigationCallback($callback);
        $app->add($navs);

        echo $app->render();
    }

}
