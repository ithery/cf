<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 14, 2019, 1:19:07 PM
 */
trait CJavascript_Validation_Trait_UseDelegatedValidatorTrait {
    /**
     * Delegated validator.
     *
     * @var CJavascript_Validation_ValidatorDelegated
     */
    protected $validator;

    /**
     * Sets delegated Validator instance.
     *
     * @param \Proengsoft\JsValidation\Support\DelegatedValidator $validator
     *
     * @return void
     */
    public function setDelegatedValidator(CJavascript_Validation_ValidatorDelegated $validator) {
        $this->validator = $validator;
    }

    /**
     * Gets current DelegatedValidator instance.
     *
     * @return CJavascript_Validation_ValidatorDelegated
     */
    public function getDelegatedValidator() {
        return $this->validator;
    }
}
