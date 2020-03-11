<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CObservable_Listener_Handler_ReloadDataTableHandler extends CObservable_Listener_Handler {

    use CObservable_Listener_Handler_Trait_TargetHandlerTrait,
        CObservable_Listener_Handler_Trait_SelectorHandlerTrait;

    public function __construct($listener) {
        parent::__construct($listener);

        $this->target = "";
        $this->selector = "";
    }

    public function js() {

        $selector = $this->getSelector();

        $js = "$('" . $selector . "').DataTable().ajax.reload()";

        return $js;
    }

}
