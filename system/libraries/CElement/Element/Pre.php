<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_Element_Pre extends CElement_Element {
    public function __construct($id = '') {
        parent::__construct($id);
        $this->tag = 'pre';
        $this->haveIndent = false;
    }

    /**
     * @param string $id
     *
     * @return CElement_Element_Pre
     */
    public static function factory($id = null) {
        return new CElement_Element_Pre($id);
    }
}
