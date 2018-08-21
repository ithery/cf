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
    protected $type;

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->header = $this->addH4();
        $this->content = $this->add_div()->addClass(' clearfix');
        $this->addClass('alert');
        $this->wrapper = $this->content;
        $this->tag = 'div';
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function build() {
        $this->header->add($this->getTranslationTitle());
        switch ($this->type) {
            case 'error':
                $this->addClass('alert-danger');
                break;
            default:
                $this->addClass('alert-success');
                break;
        }
    }

}
