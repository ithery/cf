<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 14, 2019, 12:43:37 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_Form_Validation {

    /**
     * Configuration options.
     *
     * @var array
     */
    protected $options;

    /**
     * rules.
     *
     * @var array
     */
    protected $rules;

    /**
     * Create a new Validator factory instance.
     *
     * @param array $options
     */
    public function __construct($rules, array $options = []) {
        $this->setOptions($options);
        $this->rules = $rules;
    }

    /**
     * @param $options
     * @return void
     */
    protected function setOptions($options) {
        $options['disable_remote_validation'] = empty($options['disable_remote_validation']) ? false : $options['disable_remote_validation'];
        $options['view'] = empty($options['view']) ? 'jsvalidation:bootstrap' : $options['view'];
        $options['form_selector'] = empty($options['form_selector']) ? 'form' : $options['form_selector'];
        $this->options = $options;
    }

    /**
     * Get the validator instance for the request.
     *
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidatorInstance(array $messages = [], array $customAttributes = []) {
        $data = $this->getValidationData($this->rules, $customAttributes);
        $validator = CValidation::createValidator($data, $this->rules, $messages, $customAttributes);
        $validator->addCustomAttributes($customAttributes);
        return $validator;
    }

    /**
     * Gets fake data when validator has wildcard rules.
     *
     * @param array $rules
     * @return array
     */
    protected function getValidationData(array $rules, array $customAttributes = []) {
        $attributes = array_filter(array_keys($rules), function ($attribute) {
            return $attribute !== '' && mb_strpos($attribute, '*') !== false;
        });
        $attributes = array_merge(array_keys($customAttributes), $attributes);
        $data = array_reduce($attributes, function ($data, $attribute) {
            carr::set($data, $attribute, true);
            return $data;
        }, []);
        return $data;
    }

    /**
     * Creates JsValidator instance based on Validator.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @param null|string $selector
     * @return \Proengsoft\JsValidation\Javascript\JavascriptValidator
     */
    public function validator($selector = null) {
        $validator = $this->getValidatorInstance();
        return $this->jsValidator($validator, $selector);
    }

    /**
     * Creates JsValidator instance based on Validator.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @param null|string $selector
     * @return \Proengsoft\JsValidation\Javascript\JavascriptValidator
     */
    protected function jsValidator(CValidation_Validator $validator, $selector = null) {
        $remote = !$this->options['disable_remote_validation'];
        $selector = is_null($selector) ? $this->options['form_selector'] : $selector;
        $delegated = new CJavascript_Validation_ValidatorDelegated($validator, new CJavascript_Validation_RuleParserProxy());
        $rules = new CJavascript_Validation_RuleParser($delegated, $this->getSessionToken());
        $messages = new CJavascript_Validation_MessageParser($delegated);
        $jsValidator = new CJavascript_Validation_ValidatorHandler($rules, $messages);
        $manager = new CJavascript_Validation_ValidatorJavascript($jsValidator, compact('view', 'selector', 'remote'));
        return $manager;
    }

    /**
     * Get and encrypt token from session store.
     *
     * @return null|string
     */
    protected function getSessionToken() {
        $token = CSession::instance()->id();
        return $token;
    }

}
