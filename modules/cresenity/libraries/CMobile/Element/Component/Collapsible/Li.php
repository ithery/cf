<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Collapsible_Li extends CMobile_Element_AbstractComponent {

    public function __construct($id = "") {
        parent::__construct($id);
        $this->tag = 'li';
    }

    public function add_header($id="") {
        $element = CMobile_Element_Component_Collapsible_Li_Header::factory($id);
        $this->add($element);
        return $element;
    }

    public function add_body($id="") {
        $element = CMobile_Element_Component_Collapsible_Li_Body::factory($id);
        $this->add($element);
        return $element;
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Collapsible_Li($id);
    }
}
