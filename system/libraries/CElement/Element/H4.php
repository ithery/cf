<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Element_H4 extends CElement_Element {
    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id = '') {
        parent::__construct($id);
        $this->tag = 'h4';
    }
}
