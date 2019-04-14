<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * CValidation library.
 *
 */
class CValidation {

    use CTrait_Compat_Validation;

    public static function createValidator(array $data, array $rules, array $messages = [], array $customAttributes = []) {
        return new CValidation_Validator($data, $rules, $messages, $customAttributes);
    }

}

// End CValidation
