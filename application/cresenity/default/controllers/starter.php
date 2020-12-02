<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Controller_Starter extends CController {

    public function __construct() {
        
    }

    public function index() {

        $app = CApp::instance();




        return $app;
    }

}
