<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CElement_Element_A extends CElement_Element {

    protected $href;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "a";
        $this->href = "";
    }

    
    public function set_href($href) {
        $this->href = $href;
        return $this;
    }

    public function build($indent = 0) {
        if (strlen($this->href) > 0) {
            $this->add_attr('href', $this->href);
        }
    }

}
