<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_H4 extends CMobile_Element {

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "h4";
    }

    public static function factory($id = "") {
        return new CMobile_Element_H4($id);
    }

   

}
