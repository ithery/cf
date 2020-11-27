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
        return CF::response()->view('test');
    }
    
    public function child() {
        return CF::response()->view('child');
    }
    
    public function app() {
        $app = CApp::instance();
        
        echo $app->render();
    }
}
