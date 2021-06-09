<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 12, 2019, 7:56:27 PM
 */
class CValidation_Rule {
    /**
     * Get a dimensions constraint builder instance.
     *
     * @param array $constraints
     *
     * @return CValidation_Rule_Dimension
     */
    public static function dimensions(array $constraints = []) {
        return new CValidation_Rule_Dimension($constraints);
    }

    /**
     * Get a exists constraint builder instance.
     *
     * @param string $table
     * @param string $column
     *
     * @return CValidation_Rule_Exists
     */
    public static function exists($table, $column = 'NULL') {
        return new CValidation_Rule_Exists($table, $column);
    }

    /**
     * Get an in constraint builder instance.
     *
     * @param array|string|CCollection $values
     *
     * @return CValidation_Rule_In
     */
    public static function in($values) {
        if ($values instanceof CCollection) {
            $values = $values->toArray();
        }

        return new CValidation_Rule_In(is_array($values) ? $values : func_get_args());
    }

    /**
     * Get a not_in constraint builder instance.
     *
     * @param array|string|CCollection $values
     *
     * @return CValidation_Rule_NotIn
     */
    public static function notIn($values) {
        if ($values instanceof CCollection) {
            $values = $values->toArray();
        }

        return new CValidation_Rule_NotIn(is_array($values) ? $values : func_get_args());
    }

    /**
     * Get a required_if constraint builder instance.
     *
     * @param callable $callback
     *
     * @return CValidation_Rule_RequiredIf
     */
    public static function requiredIf($callback) {
        return new CValidation_Rule_RequiredIf($callback);
    }

    /**
     * Get a unique constraint builder instance.
     *
     * @param string $table
     * @param string $column
     *
     * @return CValidation_Rule_Unique
     */
    public static function unique($table, $column = 'NULL') {
        return new CValidation_Rule_Unique($table, $column);
    }
}
