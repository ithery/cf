<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CElement_Element_Iframe extends CElement_Element {

    protected $src = "";
    protected $width = "";
    protected $height = "";

    public function __construct($id = "") {
        parent::__construct($id);
        $this->tag = "iframe";
    }

    public function set_src($src) {
        $this->src = $src;
        return $this;
    }

    public function setSrc($src)
    {
        $this->src = $src;
        return $this;
    }

    public function setWidth($value)
    {
        $this->width = $value;
        return $this;
    }

    public function setHeight($value)
    {
        $this->height = $value;
        return $this;
    }

    public function build() {
        $this->set_attr('src', $this->src);
        if ($this->width) {
            $this->set_attr('width', $this->width);
        }
        if ($this->height) {
            $this->set_attr('height', $this->height);
        }
    }

}
