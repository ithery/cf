<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 30, 2019, 3:25:25 PM
 */
trait CModel_Validating_ValidatingTrait {
    use CModel_Validating_Injector_UniqueInjectorTrait;

    /**
     * Error messages as provided by the validator.
     *
     * @var CBase_MessageBag
     */
    protected $validationErrors;

    /**
     * Whether the model should undergo validation when saving or not.
     *
     * @var bool
     */
    protected $validating = true;

    /**
     * The Validator factory class used for validation.
     *
     * @var CValidation_Validator
     */
    protected $validator;

    /**
     * Boot the trait. Adds an observer class for validating.
     *
     * @return void
     */
    public static function bootValidatingTrait() {
        static::observe(new CModel_Validating_ValidatingObserver());
    }

    /**
     * Returns whether or not the model will attempt to validate
     * itself when saving.
     *
     * @return bool
     */
    public function getValidating() {
        return $this->validating;
    }

    /**
     * Set whether the model should attempt validation on saving.
     *
     * @param bool $value
     *
     * @return void
     */
    public function setValidating($value) {
        $this->validating = (boolean) $value;
    }

    /**
     * Returns whether the model will raise an exception or
     * return a boolean when validating.
     *
     * @return bool
     */
    public function getThrowValidationExceptions() {
        return isset($this->throwValidationExceptions) ? $this->throwValidationExceptions : false;
    }

    /**
     * Set whether the model should raise an exception or
     * return a boolean on a failed validation.
     *
     * @param bool $value
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function setThrowValidationExceptions($value) {
        $this->throwValidationExceptions = (boolean) $value;
    }

    /**
     * Returns whether or not the model will add it's unique
     * identifier to the rules when validating.
     *
     * @return bool
     */
    public function getInjectUniqueIdentifier() {
        return isset($this->injectUniqueIdentifier) ? $this->injectUniqueIdentifier : true;
    }

    /**
     * Set the model to add unique identifier to rules when performing
     * validation.
     *
     * @param bool $value
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function setInjectUniqueIdentifier($value) {
        $this->injectUniqueIdentifier = (boolean) $value;
    }

    /**
     * Get the model.
     *
     * @return CModel
     */
    public function getModel() {
        return $this;
    }

    /**
     * Get the casted model attributes.
     *
     * @return array
     */
    public function getModelAttributes() {
        $attributes = $this->getModel()->getAttributes();
        foreach ($attributes as $key => $value) {
            // The validator doesn't handle Carbon instances, so instead of casting
            // them we'll return their raw value instead.
            if (in_array($key, $this->getDates()) || $this->isDateCastable($key)) {
                $attributes[$key] = $value;
                continue;
            }
            $attributes[$key] = $this->getModel()->getAttributeValue($key);
        }
        return $attributes;
    }

    /**
     * Get the custom validation messages being used by the model.
     *
     * @return array
     */
    public function getValidationMessages() {
        return isset($this->validationMessages) ? $this->validationMessages : [];
    }

    /**
     * Handy method for using the static call Model::validationMessages().
     * Protected access only to allow __callStatic to get to it.
     *
     * @return array
     */
    protected function modelValidationMessages() {
        return (new static)->getValidationMessages();
    }

    /**
     * Get the validating attribute names.
     *
     * @return array
     */
    public function getValidationAttributeNames() {
        return isset($this->validationAttributeNames) ? $this->validationAttributeNames : [];
    }

    /**
     * Handy method for using the static call Model::validationAttributeNames().
     * Protected access only to allow __callStatic to get to it.
     *
     * @return array
     */
    protected function modelValidationAttributeNames() {
        return (new static)->getValidationAttributeNames();
    }

    /**
     * Set the validating attribute names.
     *
     * @param array $attributeNames
     *
     * @return void
     */
    public function setValidationAttributeNames(array $attributeNames = null) {
        $this->validationAttributeNames = $attributeNames;
    }

    /**
     * Get the global validation rules.
     *
     * @return array
     */
    public function getRules() {
        return isset($this->rules) ? $this->rules : [];
    }

    /**
     * Get the validation rules after being prepared by the injectors.
     *
     * @return array
     */
    public function getPreparedRules() {
        return $this->injectUniqueIdentifierToRules($this->getRules());
    }

    /**
     * Handy method for using the static call Model::rules(). Protected access
     * only to allow __callStatic to get to it.
     *
     * @return array
     */
    protected function rules() {
        return $this->getRules();
    }

    /**
     * Set the global validation rules.
     *
     * @param array $rules
     *
     * @return void
     */
    public function setRules(array $rules = null) {
        $this->rules = $rules;
    }

    /**
     * Get the validation error messages from the model.
     *
     * @return CBase_MessageBag
     */
    public function getErrors() {
        return $this->validationErrors ?: new CBase_MessageBag;
    }

    /**
     * Set the error messages.
     *
     * @param CBase_MessageBag $validationErrors
     *
     * @return void
     */
    public function setErrors(CBase_MessageBag $validationErrors) {
        $this->validationErrors = $validationErrors;
    }

    /**
     * Returns whether the model is valid or not.
     *
     * @return bool
     */
    public function isValid() {
        $rules = $this->getRules();
        return $this->performValidation($rules);
    }

    /**
     * Returns if the model is valid, otherwise throws an exception.
     *
     * @return bool
     *
     * @throws \Watson\Validating\ValidationException
     */
    public function isValidOrFail() {
        if (!$this->isValid()) {
            $this->throwValidationException();
        }
        return true;
    }

    /**
     * Returns whether the model is invalid or not.
     *
     * @return bool
     */
    public function isInvalid() {
        return !$this->isValid();
    }

    /**
     * Force the model to be saved without undergoing validation.
     *
     * @param array $options
     *
     * @return bool
     */
    public function forceSave(array $options = []) {
        $currentValidatingSetting = $this->getValidating();
        $this->setValidating(false);
        $result = $this->getModel()->save($options);
        $this->setValidating($currentValidatingSetting);
        return $result;
    }

    /**
     * Perform a one-off save that will raise an exception on validation error
     * instead of returning a boolean (which is the default behaviour).
     *
     * @param array $options
     *
     * @return bool
     *
     * @throws \Throwable
     */
    public function saveOrFail(array $options = []) {
        if ($this->isInvalid()) {
            return $this->throwValidationException();
        }
        return $this->getModel()->parentSaveOrFail($options);
    }

    /**
     * Call the parent save or fail method provided by Eloquent.
     *
     * @param array $options
     *
     * @return bool
     *
     * @throws \Throwable
     */
    public function parentSaveOrFail($options) {
        return parent::saveOrFail($options);
    }

    /**
     * Perform a one-off save that will return a boolean on
     * validation error instead of raising an exception.
     *
     * @param array $options
     *
     * @return bool
     */
    public function saveOrReturn(array $options = []) {
        return $this->getModel()->save($options);
    }

    /**
     * Get the Validator instance.
     *
     * @return CValidation_Factory
     */
    public function getValidator() {
        return $this->validator ?: CValidation_Factory::instance();
    }

    /**
     * Set the Validator instance.
     *
     * @param CValidation_Factory $validator
     */
    public function setValidator(CValidation_Factory $validator) {
        $this->validator = $validator;
    }

    /**
     * Make a Validator instance for a given ruleset.
     *
     * @param array $rules
     *
     * @return CValidation_Validator
     */
    protected function makeValidator($rules = []) {
        // Get the casted model attributes.
        $attributes = $this->getModelAttributes();
        if ($this->getInjectUniqueIdentifier()) {
            $rules = $this->injectUniqueIdentifierToRules($rules);
        }
        $messages = $this->getValidationMessages();
        $validator = $this->getValidator()->make($attributes, $rules, $messages);
        if ($this->getValidationAttributeNames()) {
            $validator->setAttributeNames($this->getValidationAttributeNames());
        }
        return $validator;
    }

    /**
     * Validate the model against it's rules, returning whether
     * or not it passes and setting the error messages on the
     * model if required.
     *
     * @param array $rules
     *
     * @return bool
     *
     * @throws CModel_Validating_ValidationException
     */
    protected function performValidation($rules = []) {
        $validation = $this->makeValidator($rules);
        $result = $validation->passes();
        $this->setErrors($validation->messages());
        return $result;
    }

    /**
     * Throw a validation exception.
     *
     * @throws CModel_Validating_ValidationException
     */
    public function throwValidationException() {
        $validator = $this->makeValidator($this->getRules());
        throw new CModel_Validating_ValidationException($validator, $this);
    }

    /**
     * Update the unique rules of the global rules to
     * include the model identifier.
     *
     * @return void
     */
    public function updateRulesUniques() {
        $rules = $this->getRules();
        $this->setRules($this->injectUniqueIdentifierToRules($rules));
    }

    /**
     * If the model already exists and it has unique validations
     * it is going to fail validation unless we also pass it's
     * primary key to the rule so that it may be ignored.
     *
     * This will go through all the rules and append the model's
     * primary key to the unique rules so that the validation
     * will work as expected.
     *
     * @param array $rules
     *
     * @return array
     */
    protected function injectUniqueIdentifierToRules(array $rules) {
        foreach ($rules as $field => &$ruleset) {
            // If the ruleset is a pipe-delimited string, convert it to an array.
            $ruleset = is_string($ruleset) ? explode('|', $ruleset) : $ruleset;
            foreach ($ruleset as &$rule) {
                // Only treat stringy definitions and leave Rule classes and Closures as-is.
                if (is_string($rule)) {
                    $parameters = explode(':', $rule);
                    $validationRule = array_shift($parameters);
                    if ($method = $this->getUniqueIdentifierInjectorMethod($validationRule)) {
                        $rule = call_user_func_array(
                            [$this, $method],
                            [explode(',', carr::head($parameters)), $field]
                        );
                    }
                }
            }
        }
        return $rules;
    }

    /**
     * Get the dynamic method name for a unique identifier injector rule if it
     * exists, otherwise return false.
     *
     * @param string $validationRule
     *
     * @return mixed
     */
    protected function getUniqueIdentifierInjectorMethod($validationRule) {
        $method = 'prepare' . cstr::studly($validationRule) . 'Rule';
        return method_exists($this, $method) ? $method : false;
    }

    /**
     * Register a validating event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function validating($callback) {
        $name = static::class;

        static::registerModelEvent('validating', $callback);
    }

    /**
     * Register a validated event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function validated($callback) {
        static::registerModelEvent('validated', $callback);
    }
}
