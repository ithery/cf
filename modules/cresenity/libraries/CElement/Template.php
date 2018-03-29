<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jan 1, 2018, 4:15:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Template extends CElement {

    use CElement_Trait_Template;

    public function __construct($id, $templateName = '', $data = array()) {
        parent::__construct($id);
        $this->templateData = array();
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
