<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 14, 2018, 7:54:55 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_Terminal extends CElement_Component {

    use CElement_Trait_Template;

    protected $ajaxUrl;

    public function __construct($id = null) {
        parent::__construct($id);
        $this->templateData = array();
        $this->templateName = 'CElement/Component/Terminal';
        $this->onBeforeParse(function() {
            $data = array();
            $data['ajaxUrl'] = $this->ajaxUrl;
            $this->setData($data);
        });
    }

    public function setAjaxUrl($url) {
        $this->ajaxUrl = $url;
    }

    public function html($indent = 0) {
        return $this->getTemplateHtml();
    }

    public function js($indent = 0) {
        return $this->getTemplateJs();
    }

}
