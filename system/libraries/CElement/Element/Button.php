<?php

class CElement_Element_Button extends CElement_Element {
    public function __construct($id = '') {
        parent::__construct($id);
        $this->tag = 'button';
    }
}
