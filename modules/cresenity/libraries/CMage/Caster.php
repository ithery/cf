<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CMage_Caster {

    protected $mage;
    protected $controller;

    public function __construct(CMage_AbstractMage $mage, $controllerClass) {
        $this->mage = $mage;
        $this->controllerClass=$controllerClass;
    }

    public function index() {
        $method = $this->createMethod('Index');
        $method->execute();
    }
    
    public function add() {
        $method = $this->createMethod('Add');
        $method->execute();
    }
    
    public function edit() {
        $method = $this->createMethod('Edit');
        $method->execute();
    }

    protected function createMethod($methodName) {
        $methodClassName = 'CMage_Method_' . $methodName . 'Method';
        $methodClass = new $methodClassName($this->mage,$this->controllerClass);
        return $methodClass;
    }

}
