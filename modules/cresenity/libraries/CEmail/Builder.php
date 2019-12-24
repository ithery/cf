<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder {
    
    protected  $baseComponent;
    
    public function __construct() {
        
    }
    public function __call($method, $arguments) {
        return call_user_func_array(array($this->baseComponent,$method), $arguments);
        
    }
}