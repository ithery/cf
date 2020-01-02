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

    
    public static function createRule() {
        return new CValidation_Rule();
    }
}

// End CValidation
