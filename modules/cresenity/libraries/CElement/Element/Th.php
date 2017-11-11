<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CElement_Element_Th extends CElement_Element {

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "th";
    }

}
