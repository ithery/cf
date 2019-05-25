<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 14, 2019, 10:42:23 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Validation {

    public static function factory(array $data, array $rules = [], array $messages = [], array $customAttributes = []) {
        return static::createValidator($data, $rules, $messages, $customAttributes);
    }

}
