<?php

class CRenderable_RenderResult {
    public $html;

    public $js;

    public function __construct($html = '', $js = '') {
        $this->html = $$html;
        $this->js;
    }

    public function getHtml() {
        return $this->html;
    }

    public function getJs() {
        return $this->js;
    }

    public function merge(CRenderable_RenderResult $result) {
        $this->html .= $result->getHtml();
        $this->js .= $result->getJs();
        return $this;
    }

    public function __toString() {
        return $this->html;
    }
}
