<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Element_Br extends CElement_Element {
    public function __construct($id = '') {
        parent::__construct($id);
        $this->isOneTag = true;
        $this->tag = 'br';
    }
}
