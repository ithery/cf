<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Component_BodyComponent_Raw extends CEmail_Builder_Component_BodyComponent {
    protected static $tagName = 'c-raw';
    protected static $endingTag = true;
    protected static $rawElement = true;

    public function render() {
        return $this->getContent();
    }

}
