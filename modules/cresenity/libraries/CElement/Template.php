<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jan 1, 2018, 4:15:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Template extends CElement {

    protected $templateName;
    protected $templateData;
    protected $htmlOutput = '';
    protected $jsOutput = '';

    public function __construct($id, $templateName = '', $data = array()) {
        parent::__construct($id);
        $this->templateData = array();
        $this->templateName = $templateName;
        $this->setData($data);
    }

    public function setTemplate($name) {
        $this->templateName = $name;
        return $this;
    }

    public function theme() {
        return CF::theme();
    }

    public function getData() {
        return $this->templateData;
    }

    public function setData($data) {
        foreach ($data as $k => $v) {
            $this->setVar($k, $v);
        }
        return $this;
    }

    public function getVar($key) {
        return carr::get($this->templateData, $key);
    }

    public function setVar($key, $val) {

        $this->templateData[$key] = $val;
        return $this;
    }

    public function html($indent = 0) {
        $this->collectHtmlJs();
        return $this->htmlOutput;
    }

    public function js($indent = 0) {
        $this->collectHtmlJs();
        return $this->jsOutput;
    }

    private function getTemplatePath($templateName) {
        $viewPath = $templateName;

        return $viewPath;
    }

    private function parseTemplate($templateName) {
        $viewPath = $this->getTemplatePath($templateName);
        $view = CTemplate::factory($viewPath);
        //PMBlocks::instance()->set_data($this->templateData);
        $view->set($this->templateData);
        $output = $view->render();
        $output_js = "";
        preg_match_all('#<script>(.*?)</script>#ims', $output, $matches);

        foreach ($matches[1] as $value) {
            $output_js .= $value;
        }
        $output_html = preg_replace('#<script>(.*?)</script>#is', '', $output);

        return array(
            'html' => $output_html,
            'js' => $output_js,
        );
    }

    private function collectHtmlJs() {
        if ($this->htmlOutput == null) {
            $result_header = array();
            $result_content = array();
            $result_footer = array();


            $result_content = $this->parseTemplate($this->templateName);


            $this->htmlOutput = carr::get($result_header, 'html', '') . carr::get($result_content, 'html', '') . carr::get($result_footer, 'html', '');
            $this->jsOutput = carr::get($result_header, 'js', '') . carr::get($result_content, 'js', '') . carr::get($result_footer, 'js', '');
        }
        return true;
    }

}
