<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CMage_AbstractMethod implements CMage_MethodInterface {

    /**
     *
     * @var CMage_AbstractMage
     */
    protected $mage;
    protected $controllerClass;

    public function __construct(CMage_AbstractMage $mage,  $controllerClass) {
        $this->mage = $mage;
        $this->controllerClass = $controllerClass;
    }

    public function controllerUrl() {

        return forward_static_call(array($this->controllerClass,'controllerUrl'));
    }

}
