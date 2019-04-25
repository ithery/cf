<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 15, 2019, 1:02:13 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CValidation_Rule_NotIn {

    /**
     * The name of the rule.
     */
    protected $rule = 'not_in';

    /**
     * The accepted values.
     *
     * @var array
     */
    protected $values;

    /**
     * Create a new "not in" rule instance.
     *
     * @param  array  $values
     * @return void
     */
    public function __construct(array $values) {
        $this->values = $values;
    }

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     */
    public function __toString() {
        $values = array_map(function ($value) {
            return '"' . str_replace('"', '""', $value) . '"';
        }, $this->values);
        return $this->rule . ':' . implode(',', $values);
    }

}
