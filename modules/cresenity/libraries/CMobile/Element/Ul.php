<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Ul extends CMobile_Element {

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "ul";
    }

    public static function factory($id = "") {
        return new CMobile_Element_Ul($id);
    }

	

}
