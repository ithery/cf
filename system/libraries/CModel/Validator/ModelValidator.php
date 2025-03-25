<?php

/**
 * Class ModelValidator.
 *
 * @see https://github.com/andersao/laravel-validator
 */
class CModel_Validator_ModelValidator extends CModel_Validator_AbstractValidator {
    /**
     * Validator.
     *
     * @var \CValidation_Factory
     */
    protected $validator;

    /**
     * Construct.
     */
    public function __construct() {
        $this->validator = CValidation_Factory::instance();
    }

    /**
     * Pass the data and the rules to the validator.
     *
     * @param string $action
     *
     * @return bool
     */
    public function passes($action = null) {
        $rules = $this->getRules($action);
        $messages = $this->getMessages();
        $attributes = $this->getAttributes();
        $validator = $this->validator->make($this->data, $rules, $messages, $attributes);

        if ($validator->fails()) {
            $this->errors = $validator->messages();

            return false;
        }

        return true;
    }
}
