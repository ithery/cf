<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jan 13, 2018, 11:07:52 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CElement_Trait_Template {

    protected $templateName;
    protected $templateData;
    protected $htmlOutput = '';
    protected $jsOutput = '';
    protected $onBeforeParse = null;

    public function setTemplate($name) {
        $this->templateName = $name;
        return $this;
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

    private function getTemplatePath($templateName) {
        $viewPath = $templateName;

        return $viewPath;
    }

    private function parseTemplate($templateName) {
        if ($this->onBeforeParse != null) { 
            $callable = $this->onBeforeParse;
            $callable();
        }

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

    private function collectHtmlJsOnce() {
        if ($this->htmlOutput == null) {
            $this->collectHtmlJs();
        }
        return true;
    }

    protected function collectHtmlJs() {

        $result_header = array();
        $result_content = array();
        $result_footer = array();


        $result_content = $this->parseTemplate($this->templateName);


        $this->htmlOutput = carr::get($result_header, 'html', '') . carr::get($result_content, 'html', '') . carr::get($result_footer, 'html', '');
        $this->jsOutput = carr::get($result_header, 'js', '') . carr::get($result_content, 'js', '') . carr::get($result_footer, 'js', '');

        return true;
    }

    public function onBeforeParse(callable $callable) {
        $this->onBeforeParse = $callable;
        return $this;
    }

    public function getTemplateHtml($indent = 0) {
        $this->collectHtmlJsOnce();
        return $this->htmlOutput;
    }

    public function getTemplateJs($indent = 0) {
        $this->collectHtmlJsOnce();
        return $this->jsOutput;
    }

}
