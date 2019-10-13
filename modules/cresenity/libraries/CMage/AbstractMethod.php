<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CMage_AbstractMethod implements CMage_MethodInterface {
    /**
     *
     * @var CMage_Option 
     */
    protected $option;
    
    public function __construct($option) {
        $this->option=$option;
    }
}