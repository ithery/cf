<?php

class CValidation_Rule_Can implements CValidation_RuleInterface, CValidation_Contract_ValidatorAwareRuleInterface {
    /**
     * The ability to check.
     *
     * @var string
     */
    protected $ability;

    /**
     * The arguments to pass to the authorization check.
     *
     * @var array
     */
    protected $arguments;

    /**
     * The current validator instance.
     *
     * @var \CValidation_Validator
     */
    protected $validator;

    /**
     * Constructor.
     *
     * @param string $ability
     * @param array  $arguments
     */
    public function __construct($ability, array $arguments = []) {
        $this->ability = $ability;
        $this->arguments = $arguments;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value) {
        $arguments = $this->arguments;

        $model = array_shift($arguments);

        return c::gate()->allows($this->ability, array_filter([$model, ...$arguments, $value]));
    }

    /**
     * Get the validation error message.
     *
     * @return array
     */
    public function message() {
        $message = $this->validator->getTranslator()->get('validation.can');

        return $message === 'validation.can'
            ? ['The :attribute field contains an unauthorized value.']
            : $message;
    }

    /**
     * Set the current validator.
     *
     * @param \CValidation_Validator $validator
     *
     * @return $this
     */
    public function setValidator($validator) {
        $this->validator = $validator;

        return $this;
    }
}
