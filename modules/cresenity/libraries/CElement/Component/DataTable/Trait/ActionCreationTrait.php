<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CElement_Component_DataTable_Trait_ActionCreationTrait {
    
    
    public function createExportAction($options) {
        $id = carr::get($options,'id');
        $act = CElement_Factory::createComponent('Action', $id)->setLabel('Export');
        
        
        
        return $act;
    }
}
