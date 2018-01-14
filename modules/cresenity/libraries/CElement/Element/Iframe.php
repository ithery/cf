<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CElement_Element_Iframe extends CElement_Element {

    protected $src = "";

    public function __construct($id = "") {
        parent::__construct($id);
        $this->tag = "iframe";
    }

    public function set_src($src) {
        $this->src = $src;
        return $this;
    }

    public function build() {
        $this->set_attr('src', $this->src);
    }

}
