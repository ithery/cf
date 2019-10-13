<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CMage_AbstractMethod implements CMage_MethodInterface {
    /**
     *
     * @var CMage 
     */
    protected $mage;
    
    public function __construct($mage) {
        
    }
}