<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CElement_Component_Blockly extends CElement_Component {

    use CTrait_Element_Property_Height;
    use CTrait_Element_Property_Width;
    
    protected $mediaDirectory;
    protected $toolbox;

    protected function createToolbox() {
        $toolbox = new CElement_Component_Blockly_Toolbox();
        return $toolbox;
    }
    
    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->tag = 'div';
        $this->height = '480';
        $this->width = '400';
        $this->toolbox = $this->createToolbox();
        
    }

    public function build() {
        $this->customCss('width', $this->width . 'px');
        $this->customCss('white-space', $this->height . 'px');
        $this->add($this->toolbox);
    }

}
