<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CElement_Element_Iframe extends CElement_Element {

    use CTrait_Element_Property_Width,
        CTrait_Element_Property_Height;

    protected $src = "";

    public function __construct($id = "") {
        parent::__construct($id);
        $this->tag = "iframe";
    }

    public function set_src($src) {
        $this->src = $src;
        return $this;
    }

    public function setSrc($src) {
        $this->src = $src;
        return $this;
    }

    public function build() {
        $this->setAttr('src', $this->src);
        if ($this->width) {
            $this->setAttr('width', $this->width);
        }
        if ($this->height) {
            $this->setAttr('height', $this->height);
        }
    }

}
