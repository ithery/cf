<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CElement_Component_PrismCode extends CElement_Component {

    use CTrait_Element_Property_Height;
    use CTrait_Element_Property_Width;

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->tag = 'div';
        $this->height = '480';
        $this->width = '400';
    }

}
