<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class CMage_Mage_FilterCollection extends CCollection{
    
    protected $mage;
   
    
    public function __construct($items = [],$mage) {
        parent::__construct($items);
        $this->mage=$mage;
       
    }
    
    public function addFilter($name) {
        $filter = new CMage_Filter($name);
        $this->push($filter);
        return $filter;
    }

   
}