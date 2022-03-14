<?php
class CJavascript_Validation_Remote_Resolver {
    use CJavascript_Validation_Trait_AccessProtectedTrait;

    /**
     * @var \Closure
     */
    protected $resolver;

    /**
     * Whether to escape validation messages.
     *
     * @var bool
     */
    protected $escape;

    /**
     * RemoteValidator constructor.
     *
     * @param bool $escape
     */
    public function __construct(CValidation_Factory $factory, $escape = false) {
        $this->resolver = $this->getProtected($factory, 'resolver');
        $this->escape = $escape;
    }

    /**
     * Closure used to resolve Validator instance.
     *
     * @param $field
     *
     * @return \Closure
     */
    public function resolver($field) {
        return function ($data, $rules, $messages, $customAttributes) use ($field) {
            return $this->resolve($data, $rules, $messages, $customAttributes, $field);
        };
    }

    /**
     * Resolves Validator instance.
     *
     * @param $data
     * @param $rules
     * @param $messages
     * @param $customAttributes
     * @param $field
     *
     * @return \CValidation_Validator
     */
    protected function resolve($data, $rules, $messages, $customAttributes, $field) {
        $validateAll = carr::get($data, $field . '_validate_all', false);
        $validationRule = 'bail|' . CJavascript_Validation_Remote_Validator::EXTENSION_NAME . ':' . $validateAll;
        $rules = [$field => $validationRule] + $rules;
        $validator = $this->createValidator($data, $rules, $messages, $customAttributes);

        return $validator;
    }

    /**
     * Create new validator instance.
     *
     * @param $data
     * @param $rules
     * @param $messages
     * @param $customAttributes
     *
     * @return \CValidation_Validator
     */
    protected function createValidator($data, $rules, $messages, $customAttributes) {
        if (is_null($this->resolver)) {
            return new CValidation_Validator($data, $rules, $messages, $customAttributes);
        }

        return call_user_func($this->resolver, $data, $rules, $messages, $customAttributes);
    }

    /**
     * Closure used to trigger JsValidations.
     *
     * @return \Closure
     */
    public function validatorClosure() {
        return function ($attribute, $value, $parameters, CValidation_Validator $validator) {
            $remoteValidator = new CJavascript_Validation_Remote_Validator($validator, $this->escape);
            $remoteValidator->validate($value, $parameters);

            return $attribute;
        };
    }
}
