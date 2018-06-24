<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2018, 5:55:14 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDatabase_Query_Expression {

    /**
     * The value of the expression.
     * @var mixed
     */
    protected $value;

    /**
     * Create a new raw query expression.
     * @param  mixed  $value
     * @return void
     */
    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * Get the value of the expression.
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Get the value of the expression.
     *
     * @return string
     */
    public function __toString() {
        return (string) $this->getValue();
    }

}
