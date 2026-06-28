<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @deprecated use CElement_View instead
 */
class CElement_Template extends CElement {
    use CElement_Trait_Template;

    /**
     * @param string $id
     * @param string $templateName
     * @param array  $data
     */
    public function __construct($id, $templateName = '', $data = []) {
        parent::__construct($id);
        $this->templateData = [];
        $this->templateName = $templateName;
        $this->setData($data);
    }

    public function html($indent = 0) {
        return $this->getTemplateHtml();
    }

    public function js($indent = 0) {
        return $this->getTemplateJs();
    }
}
