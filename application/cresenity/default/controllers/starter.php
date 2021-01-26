<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Controller_Starter extends CController {

    public function __construct() {
        $app = CApp::instance();
        $app->setLoginRequired(false);
        $app->setTheme('cresenity-starter');
        $app->setView('starter');
    }

    public function index() {

        $app = CApp::instance();


        
        $view = $app->addView('starter.content');
        $viewElement = $view->viewElement('field-email');
        $table= $viewElement->addTable();
        $table->addColumn('name');
        
        

        return $app;
    }

    
    public function alpine() {
        return c::view('alpine');
    }
}
