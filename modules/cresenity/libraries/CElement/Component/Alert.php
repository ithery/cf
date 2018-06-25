<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CElement_Component_Alert extends CElement_Component {

    use CTrait_Element_Property_Title;

    protected $header;
    protected $content;

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->header = $this->addH4();
        $this->add($this->header);
        $this->content = $this->add_div()->addClass('widget-content clearfix');
        $this->addClass('alert');
        $this->wrapper = $this->content;
        $this->tag = 'div';
    }

    public function build() {
        $this->header->add($this->getTranslationTitle());
    }

}
