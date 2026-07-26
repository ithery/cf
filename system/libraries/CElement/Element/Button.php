<?php

class CElement_Element_Button extends CElement_Element {
    /**
     * @var string
     */
    protected $themeType = 'button';

    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id = '') {
        parent::__construct($id);
        $this->tag = 'button';
    }
}
