<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CMage_Method_IndexMethod extends CMage_AbstractMethod {
    
    public function execute() {
        $app = CApp::instance();
        
        $app->setTitle($this->option->getTitle());
        
        echo $app->render();
    }

}