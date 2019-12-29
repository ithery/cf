<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CElement_Component_Blockly extends CElement_Component {

    protected $mediaDirectory;
    protected $toolbox;
    protected $toolbar;
    protected $blocklyWrapper;
    protected $saveAction;

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        if (!CManager::isRegisteredModule('blockly')) {
            CManager::registerModule('blockly');
        }
        $this->tag = 'div';
        $this->toolbox = new CElement_Component_Blockly_Toolbox();
        $this->toolbar = new CElement_List_ActionList();
        $this->blocklyWrapper = new CElement_Element_Div();

        $this->addClass('capp-blockly');
        $this->blocklyWrapper->customCss('height', '600px');
        $this->add($this->toolbar);
        $this->blocklyWrapper->add($this->toolbox);
        $this->add($this->blocklyWrapper);
        $this->saveAction = $this->toolbar->addAction()->setLabel('Save');
    }

    public function build() {
        
    }

    public function js($indent = 0) {
        $toolboxId = $jsOptions = [];
        $jsOptions['blocklyElementId'] = $this->blocklyWrapper->id();
        $jsOptions['toolboxElementId'] = $this->toolbox->id();
        $jsOptions['saveElementId'] = $this->saveAction->id();
        $jsOptions['mediaFolder'] = '/modules/cresenity/media/js/blockly/media/';
        return "
            new CBlockly(" . json_encode($jsOptions) . ");
        ";
    }

}
