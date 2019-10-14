<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CMage_Mage_FieldCollection extends CCollection {
    
    protected $mage;
   
    public function __construct($mage) {
        $this->mage=$mage;
      
    }
    
    public function addField($name) {
        $field = new CMage_Field($name);
        $this->push($field);
        return $field;
    }

}