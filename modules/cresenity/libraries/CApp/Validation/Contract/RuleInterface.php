<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 4:28:53 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CApp_Validation_Contract_RuleInterface {

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value);

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message();
}
