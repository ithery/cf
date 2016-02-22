<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_A extends CMobile_Element {

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "a";
    }

    public static function factory($id = "") {
        return new CMobile_Element_A($id);
    }

	

}
