<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CMage_Mage_FieldCollection extends CCollection {
    
    protected $mage;
   
    public function __construct($items = [],$mage=null) {
        parent::__construct($items);
        $this->mage=$mage;
      
    }
    
    public function addField($name) {
        $field = new CMage_Field($name);
        $this->push($field);
        return $field;
    }

    public function fillModelFromRequest($model,$request) {
        foreach($this->items as $field) {
           
            $model->{$field->getName()} = carr::get($request,$field->getName());
        }
        return $model;
    }
}