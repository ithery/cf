<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_H3 extends CMobile_Element {

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "h3";
    }

    public static function factory($id = "") {
        return new CMobile_Element_H3($id);
    }

   

}
