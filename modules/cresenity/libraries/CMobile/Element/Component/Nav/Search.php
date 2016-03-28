<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Nav_Search extends CMobile_Element_AbstractComponent {

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Nav_Search($id);
    }

    public function build() {
        
    }

}
