<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Component_HeadComponent_Head extends CEmail_Builder_Component_HeadComponent {

    protected static $tagName = 'c-head';

    public function handler() {
        return $this->handlerChildren();
    }

}
