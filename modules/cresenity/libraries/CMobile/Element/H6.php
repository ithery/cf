<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_H6 extends CMobile_Element {

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "h6";
    }

    public static function factory($id = "") {
        return new CMobile_Element_H6($id);
    }

   

}
