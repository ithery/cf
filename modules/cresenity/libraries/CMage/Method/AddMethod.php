<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class CMage_Method_AddMethod extends CMage_AbstractMethod {

    public function execute() {
        $app = CApp::instance();


        
        $app->setTitle('Add '.$this->mage->getTitle());

        
        
        $form = $app->addForm();
        foreach($this->mage->fields() as $field) {
           
            
        }
        
        
        echo $app->render();
    }

}
