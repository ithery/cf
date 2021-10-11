<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 14, 2018, 7:54:55 PM
 */
class CElement_Component_Terminal extends CElement_Component {
    use CApp_Trait_Template,
        CTrait_Element_Property_Height;

    protected $ajaxUrl;

    protected $ajaxMethod;

    protected $greetings = '';

    protected $prompt = '';

    public function __construct($id = null) {
        parent::__construct($id);
        $this->templateData = [];
        $this->templateName = 'CElement/Component/Terminal';
        $this->greetings = '';
        $this->prompt = '';
        $this->height = '400';
        $this->ajaxMethod = 'post';

        CManager::instance()->registerModule('terminal');
        $this->onBeforeParse(function () {
            $data = [];
            $data['ajaxUrl'] = $this->ajaxUrl;
            $data['ajaxMethod'] = $this->ajaxMethod;
            $data['prompt'] = $this->prompt;
            $data['greetings'] = $this->greetings;
            $data['height'] = $this->height;
            $data['elementId'] = $this->id;
            $this->setData($data);
        });
    }

    public function setAjaxUrl($url) {
        $this->ajaxUrl = $url;
    }

    public function setAjaxMethod($method) {
        $this->ajaxMethod = $method;
    }

    public function setGreetings($greetings) {
        $this->greetings = $greetings;
    }

    public function setPrompt($prompt) {
        $this->prompt = $prompt;
    }

    public function html($indent = 0) {
        return $this->getTemplateHtml();
    }

    public function js($indent = 0) {
        return $this->getTemplateJs();
    }
}
