<?php

class CJavascript_Validation_Remote_Validator {
    use CJavascript_Validation_Trait_AccessProtectedTrait,
        CJavascript_Validation_Trait_RuleListTrait;

    /**
     * Validator extension name.
     */
    const EXTENSION_NAME = 'jsvalidation';

    /**
     * @var \CValidation_Validator
     */
    protected $validator;

    /**
     * Whether to escape validation messages.
     *
     * @var bool
     */
    protected $escape;

    /**
     * RemoteValidator constructor.
     *
     * @param \CValidation_Validator $validator
     * @param bool                   $escape
     */
    public function __construct(CValidation_Validator $validator, $escape = false) {
        $this->validator = $validator;
        $this->escape = $escape;
    }

    /**
     * Validate request.
     *
     * @param $field
     * @param $parameters
     *
     * @throws \CValidation_Exception
     *
     * @return void
     */
    public function validate($field, $parameters = []) {
        $attribute = $this->parseAttributeName($field);
        $validationParams = $this->parseParameters($parameters);
        $validationResult = $this->validateJsRemoteRequest($attribute, $validationParams);

        $this->throwValidationException($validationResult, $this->validator);
    }

    /**
     * Throw the failed validation exception.
     *
     * @param mixed                 $result
     * @param \Calidation_Validator $validator
     *
     * @throws \CValidation_Exception|\CHTTP_Exception_ResponseException
     *
     * @return void
     */
    protected function throwValidationException($result, $validator) {
        $response = new CHTTP_JsonResponse($result, 200);

        if ($result !== true && class_exists(CValidation_Exception::class)) {
            throw new CValidation_Exception($validator, $response);
        }

        throw new CHTTP_Exception_ResponseException($response);
    }

    /**
     *  Parse Validation input request data.
     *
     * @param $data
     *
     * @return array
     */
    protected function parseAttributeName($data) {
        parse_str($data, $attrParts);
        $attrParts = is_null($attrParts) ? [] : $attrParts;
        $newAttr = array_keys(carr::dot($attrParts));

        return array_pop($newAttr);
    }

    /**
     *  Parse Validation parameters.
     *
     * @param $parameters
     *
     * @return array
     */
    protected function parseParameters($parameters) {
        $newParams = ['validate_all' => false];
        if (isset($parameters[0])) {
            $newParams['validate_all'] = ($parameters[0] === 'true') ? true : false;
        }

        return $newParams;
    }

    /**
     * Validate remote Javascript Validations.
     *
     * @param $attribute
     * @param array $parameters
     *
     * @return array|bool
     */
    protected function validateJsRemoteRequest($attribute, $parameters) {
        $this->setRemoteValidation($attribute, $parameters['validate_all']);

        $validator = $this->validator;
        if ($validator->passes()) {
            return true;
        }

        $messages = $validator->messages()->get($attribute);

        if ($this->escape) {
            foreach ($messages as $key => $value) {
                $messages[$key] = c::e($value);
            }
        }

        return $messages;
    }

    /**
     * Sets data for validate remote rules.
     *
     * @param $attribute
     * @param bool $validateAll
     *
     * @return void
     */
    protected function setRemoteValidation($attribute, $validateAll = false) {
        $validator = $this->validator;
        $rules = $validator->getRules();
        $rules = isset($rules[$attribute]) ? $rules[$attribute] : [];
        if (in_array('no_js_validation', $rules)) {
            $validator->setRules([$attribute => []]);

            return;
        }
        if (!$validateAll) {
            $rules = $this->purgeNonRemoteRules($rules, $validator);
        }
        $validator->setRules([$attribute => $rules]);
    }

    /**
     * Remove rules that should not be validated remotely.
     *
     * @param $rules
     * @param CValidation_Validator $validator
     *
     * @return mixed
     */
    protected function purgeNonRemoteRules($rules, $validator) {
        $protectedValidator = $this->createProtectedCaller($validator);

        foreach ($rules as $i => $rule) {
            $parsedRule = CValidation_RuleParser::parse($rule);
            if (!$this->isRemoteRule($parsedRule[0])) {
                unset($rules[$i]);
            }
        }

        return $rules;
    }
}
