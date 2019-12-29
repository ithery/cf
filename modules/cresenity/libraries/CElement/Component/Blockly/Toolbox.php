<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use CElement_Component_Blockly_BlockHelper as BlockHelper;
use CElement_Component_Blockly_CategoryHelper as CategoryHelper;
use CElement_Component_Blockly_ToolboxHelper as ToolboxHelper;

class CElement_Component_Blockly_Toolbox extends CElement_Element {

    protected $categories = [];

    public function __construct($id = "", $tag = "div") {
        parent::__construct($id, $tag);
        $this->tag = 'xml';
        $this->categories = [];
    }

    public function build() {
      
        $this->categories[] = ToolboxHelper::getAllCategoryData();
    }

    public function html($indent = 0) {
        $this->buildOnce();
        $xmlOpen = '<xml id="' . $this->id . '" style="display: none">';
        $xmlClose = '</xml>';
       
        $categoryXml = carr::reduce($this->categories, function($output,$blockArray, $name) {
                    return $output.CategoryHelper::renderCategory($name, $blockArray);
                },'');
        return $xmlOpen . $categoryXml . $xmlClose;
    }

}
