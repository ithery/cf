<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_H2 extends CMobile_Element {

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "h2";
    }

    public static function factory($id = "") {
        return new CMobile_Element_H2($id);
    }

   

}
