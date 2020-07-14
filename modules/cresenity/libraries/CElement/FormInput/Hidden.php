<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CElement_FormInput_Hidden extends CElement_FormInput {

    public function __construct($id) {
        parent::__construct($id);
        $this->type = "hidden";
 
    }

    protected function build() {
        $this->setAttr('type', $this->type);
        $this->setAttr('value', $this->value);
    }

}
