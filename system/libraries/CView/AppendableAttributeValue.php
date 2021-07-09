<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Dec 6, 2020
 */
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
