<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Collapsible_li_Body extends CMobile_Element_AbstractComponent {

    public function __construct($id = "") {
        parent::__construct($id);
        $this->tag = "div";
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Collapsible_li_Body($id);
    }

    // public function build() {
    //     $this->add_class('collapsible-body');
    // }

    public function html($indent=0) {
        $this->add_class('collapsible-body');
        $html = new CStringBuilder();
        $html->appendln(parent::html() . '');
        return $html->text();
    }
}
