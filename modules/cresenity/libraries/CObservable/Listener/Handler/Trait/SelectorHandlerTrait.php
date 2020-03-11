<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CObservable_Listener_Handler_Trait_SelectorHandlerTrait {

    /**
     * id of handler targeted renderable
     * @var string
     */
    protected $selector;

    public function setSelector($selector) {

        $this->selector = $selector;

        return $this;
    }

    
    public function getSelector() {
        if($this->selector!=null) {
            return $this->selector;
        }
        
        if(c::hasTrait($this,CObservable_Listener_Handler_Trait_TargetHandlerTrait::class)) {
            if(strlen($this->target)>0) {
                return '#'.$this->target;
            }
        }
        return '#';
    }
}
