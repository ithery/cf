<?php

/**
 * Description of home
 *
 * @author Hery
 */
Class Controller_Home extends CController {

    public function index() {
        $app = CApp::instance();
        $app->setLoginRequired(false);
        $app->setView('welcome');
        
        return $app;
    }

    public function upload() {
        $app = CApp::instance();
        $app->setLoginRequired(false);
        CManager::registerModule('bootstrap-4');
        $app->setViewName('test.upload');
        return $app;
    }

    public function validate() {
        $app = CApp::instance();
        $app->setLoginRequired(false);
        CManager::registerModule('bootstrap-4');
        $app->setViewName('test.validate');
        return $app;
    }

    public function test() {
        $app = CApp::instance();
        $app->setLoginRequired(false);
        $app->setViewName('test2');
        $app->setTheme('cfdocs');
        //$app->addComponent("counter");
        echo $app->render();
    }

    public function member() {
        $app = CApp::instance();
        $app->setLoginRequired(false);
        $app->setViewName('test');
        $app->setTheme('cfdocs');


        $app->addView('member', [
            'members' => \Cresenity\Testing\MemberModel::all()
        ]);

        return $app;
    }

    public function component() {
        $app = CApp::instance();
        $app->setLoginRequired(false);
        $app->setViewName('test');
        $app->setTheme('cfdocs');
        /*
          CManager::registerModule('jquery-3.2.1');
         *  
         */
        CManager::registerModule('bootstrap-4');

        //$div = $app->addDiv()->setAttr('style','width:100px');
        //$div->addComponent('member-table');
        //$template = $app->addTemplate()->setTemplate('testing');




        return $app;
    }

    public function child() {
        return CF::response()->view('child');
    }

    public function app() {
        $app = CApp::instance();

        echo $app->render();
    }

}
