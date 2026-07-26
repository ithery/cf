<?php

defined('SYSPATH') or die('No direct access allowed.');


class CElement_Component_Terminal extends CElement_Component {
    use CApp_Trait_Template,
        CTrait_Element_Property_Height;

    /**
     * @var string|null
     */
    protected $ajaxUrl;

    /**
     * @var string
     */
    protected $ajaxMethod;

    /**
     * @var string
     */
    protected $greetings = '';

    /**
     * @var string
     */
    protected $prompt = '';

    /**
     * @param string|null $id
     *
     * @return void
     */
    public function __construct($id = null) {
        parent::__construct($id);
        $this->templateData = [];
        $this->templateName = 'CElement/Component/Terminal';
        $this->greetings = '';
        $this->prompt = '';
        $this->height = 400;
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

    /**
     * @param string $url
     *
     * @return void
     */
    public function setAjaxUrl($url) {
        $this->ajaxUrl = $url;
    }

    /**
     * @param string $method
     *
     * @return void
     */
    public function setAjaxMethod($method) {
        $this->ajaxMethod = $method;
    }

    /**
     * @param string $greetings
     *
     * @return void
     */
    public function setGreetings($greetings) {
        $this->greetings = $greetings;
    }

    /**
     * @param string $prompt
     *
     * @return void
     */
    public function setPrompt($prompt) {
        $this->prompt = $prompt;
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        return $this->getTemplateHtml();
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function js($indent = 0) {
        return $this->getTemplateJs();
    }
}
