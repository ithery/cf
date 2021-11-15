<?php

use CElement_Component_Blockly_BlockHelper as BlockHelper;
use CElement_Component_Blockly_ToolboxHelper as ToolboxHelper;
use CElement_Component_Blockly_CategoryHelper as CategoryHelper;

class CElement_Component_Blockly_Toolbox extends CElement_Element {
    protected $categories = [];

    public function __construct($id = '', $tag = 'div') {
        parent::__construct($id, $tag);
        $this->tag = 'xml';
        $this->categories = [];
    }

    public function build() {
        $this->categories = ToolboxHelper::getAllCategoryData();
    }

    public function html($indent = 0) {
        $this->buildOnce();
        $xmlOpen = '<xml id="' . $this->id . '" style="display: none">';
        $xmlClose = '</xml>';

        $categoryXml = carr::reduce($this->categories, function ($output, $blockArray, $name) {
            return $output . CategoryHelper::renderCategory($name, $blockArray);
        }, '');

        $sepXml = '<sep></sep>';

        $customCategoriesXml = $sepXml . '

            <category name="' . ucfirst(CategoryHelper::CATEGORY_VARIABLES) . '" colour="' . CategoryHelper::$categoryHue[CategoryHelper::CATEGORY_VARIABLES] . '" custom="VARIABLE"></category>
            <category name="' . ucfirst(CategoryHelper::CATEGORY_PROCEDURES) . '" colour="' . CategoryHelper::$categoryHue[CategoryHelper::CATEGORY_PROCEDURES] . '" custom="PROCEDURE"></category>';

        return $xmlOpen . $categoryXml . $customCategoriesXml . $xmlClose;
    }
}
