<?php

/**
 * Description of home
 *
 * @author Hery
 */
use League\Csv\Writer;

Class Controller_Home extends CController {

    public function index() {

        return CF::response()->view('welcome');
    }

    
    public function test() {
        $app = CApp::instance();
        $app->setLoginRequired(false);
        $app->setViewName('test');
        
        $app->add("Hallo");
        echo $app->render();
        
    }
    
    public function child() {
        return CF::response()->view('child');
    }
    
    public function app() {
        $app = CApp::instance();
        
        echo $app->render();
    }
}
