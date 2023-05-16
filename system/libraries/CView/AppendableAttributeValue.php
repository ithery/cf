<?php

defined('SYSPATH') or die('No direct access allowed.');

class CView_AppendableAttributeValue {
    /**
     * The attribute value.
     *
     * @var mixed
     */
    public $value;

    /**
     * Create a new appendable attribute value.
     *
     * @param mixed $value
     *
     * @return void
     */
    public function __construct($value) {
        $this->value = $value;
    }
}
