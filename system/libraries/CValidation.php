<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * CValidation library.
 *
 */
class CValidation {

    use CTrait_Compat_Validation;

    /**
     * 
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return \CValidation_Validator
     */
    public static function createValidator(array $data, array $rules, array $messages = [], array $customAttributes = []) {
        return new CValidation_Validator($data, $rules, $messages, $customAttributes);
    }

    /**
     * 
     * @return \CValidation_Rule
     */
    public static function createRule() {
        return new CValidation_Rule();
    }

    /**
     * Return validation factory or validator given by the parameter
     *
     * @param  array  $data | optional
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @return CValidation_Factory|CValidation_Validator
     */
    public static function factory($data = null, array $rules = null, array $messages = [], array $customAttributes = []) {
        if ($data != null) {
            return CValidation_Factory::instance()->make($data, $rules, $messages, $customAttributes);
        }
        return CValidation_Factory::instance();
    }

}

// End CValidation
