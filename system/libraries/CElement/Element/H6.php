<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Element_H6 extends CElement_Element {
    public function __construct($id = '') {
        parent::__construct($id);
        $this->tag = 'h6';
    }
}
