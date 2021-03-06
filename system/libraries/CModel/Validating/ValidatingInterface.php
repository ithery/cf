<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 30, 2019, 3:38:52 PM
 */
interface CModel_Validating_ValidatingInterface {
    /**
     * Returns whether or not the model will attempt to validate
     * itself when saving.
     *
     * @return bool
     */
    public function getValidating();

    /**
     * Set whether the model should attempt validation on saving.
     *
     * @param bool $value
     *
     * @return void
     */
    public function setValidating($value);

    /**
     * Returns whether the model will raise an exception or
     * return a boolean when validating.
     *
     * @return bool
     */
    public function getThrowValidationExceptions();

    /**
     * Set whether the model should raise an exception or
     * return a boolean on a failed validation.
     *
     * @param bool $value
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function setThrowValidationExceptions($value);

    /**
     * Returns whether or not the model will add it's unique
     * identifier to the rules when validating.
     *
     * @return bool
     */
    public function getInjectUniqueIdentifier();

    /**
     * Set the model to add unique identifier to rules when performing
     * validation.
     *
     * @param bool $value
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function setInjectUniqueIdentifier($value);

    /**
     * Get the model.
     *
     * @return \CModel
     */
    public function getModel();

    /**
     * Get the casted model attributes.
     *
     * @return array
     */
    public function getModelAttributes();

    /**
     * Get the global validation rules.
     *
     * @return array
     */
    public function getRules();

    /**
     * Set the global validation rules.
     *
     * @param array $rules
     *
     * @return void
     */
    public function setRules(array $rules = null);

    /**
     * Get the validation error messages from the model.
     *
     * @return \CBase_MessageBag
     */
    public function getErrors();

    /**
     * Set the error messages.
     *
     * @param \CBase_MessageBag $validationErrors
     *
     * @return void
     */
    public function setErrors(CBase_MessageBag $validationErrors);

    /**
     * Returns whether the model is valid or not.
     *
     * @return bool
     */
    public function isValid();

    /**
     * Returns if the model is valid, otherwise throws an exception.
     *
     * @throws \Watson\Validating\ValidationException
     *
     * @return bool
     */
    public function isValidOrFail();

    /**
     * Returns whether the model is invalid or not.
     *
     * @return bool
     */
    public function isInvalid();

    /**
     * Force the model to be saved without undergoing validation.
     *
     * @return bool
     */
    public function forceSave();

    /**
     * Perform a one-off save that will raise an exception on validation error
     * instead of returning a boolean (which is the default behaviour).
     *
     * @throws \Watson\Validating\ValidatingException
     *
     * @return void
     */
    public function saveOrFail();

    /**
     * Perform a one-off save that will return a boolean on
     * validation error instead of raising an exception.
     *
     * @return bool
     */
    public function saveOrReturn();

    /**
     * Get the Validator instance.
     *
     * @return CValidation_Factory
     */
    public function getValidator();

    /**
     * Set the Validator instance.
     *
     * @param CValidation_Factory $validator
     */
    public function setValidator(CValidation_Factory $validator);

    /**
     * Throw a validation exception.
     *
     * @throws \Watson\Validating\ValidationException
     */
    public function throwValidationException();

    /**
     * Update the unique rules of the global rules to
     * include the model identifier.
     *
     * @return void
     */
    public function updateRulesUniques();
}
