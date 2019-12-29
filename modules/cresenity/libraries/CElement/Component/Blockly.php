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
    protected $toolboxPosition;

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->tag = 'div';
        $this->height = '600';
        $this->width = '400';
        $this->toolbox = new CElement_Component_Blockly_Toolbox();
    }

    public function build() {
        $this->addClass('capp-blockly');
        //$this->customCss('width', $this->width . 'px');
        $this->customCss('height', $this->height . 'px');
        $this->add($this->toolbox);
    }

    public function js($indent = 0) {
        $toolboxId = $this->toolbox->id();
        return "
            Blockly.inject('" . $this->id . "', {
                grid:{
                    spacing: 25,
                    length: 3,
                    colour: '#ccc',
                    snap: true
                },
                media: '/application/devcloud/default/media/js/blockly/media/',
                toolbox: document.getElementById('" . $toolboxId . "'),
                //toolboxPosition: 'left',
                //horizontalLayout: true,
                //scrollbars: true,
            });
            //Blockly.Variables.predefinedVars.push('A');
        ";
    }

}
