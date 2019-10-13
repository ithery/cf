<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CMage_Caster {
    protected $mage;
    public function __construct(CMage_AbstractMage $mage) {
        $this->mage=$mage;
    }

    
    public function index() {
        $method = $this->createMethod('Index');
        $method->execute();
        
    }
    
    
    protected function createMethod($methodName) {
        $methodClassName='CMage_Method_'.$methodName.'Method';
        $methodClass = new $methodClassName($this->option);
        return $methodClass;
        
    }
}
