<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Slick_Item extends CMobile_Element_AbstractComponent {

    public function __construct($id = "") {
        parent::__construct($id);
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Slick_Item($id);
    }
}
