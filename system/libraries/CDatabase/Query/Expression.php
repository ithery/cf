<?php

defined('SYSPATH') or die('No direct access allowed.');

class CDatabase_Query_Expression implements CDatabase_Contract_Query_ExpressionInterface {
    /**
     * The value of the expression.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Create a new raw query expression.
     *
     * @param mixed $value
     *
     * @return void
     */
    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * Get the value of the expression.
     *
     * @return mixed
     */
    public function getValue(CDatabase_Grammar $grammar) {
        return $this->value;
    }
}
