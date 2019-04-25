<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 14, 2019, 3:38:20 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJavascript_Validation_Remote {

    use CJavascript_Validation_Trait_AccessProtectedTrait,
        CJavascript_Validation_Trait_RuleListTrait;

    /**
     * Validator extension name.
     */
    const EXTENSION_NAME = 'jsvalidation';

    /**
     * @var CValidation_Validator
     */
    protected $validator;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var array
     */
    protected $data;

    /**
     * RemoteValidator constructor.
     *
     * @param array $validationData
     */
    public function __construct(array $data, array $rules, array $messages = [], array $customAttributes = []) {
        $this->field = '_jsvalidation';
        $this->data = $data;
        $validateAll = carr::get($data, $this->field . '_validate_all', false);
        $validationRule = 'bail|' . static::EXTENSION_NAME . ':' . $validateAll;
        $rules = [$this->field => $validationRule] + $rules;
        $this->validator = new CValidation_Validator($data, $rules, $messages, $customAttributes);
    }

    /**
     * Validate request.
     *
     * @param $field
     * @param $parameters
     * @return void
     *
     * @throws CValidation_Exception
     */
    public function validate($parameters = []) {
        $field = carr::get($this->data, $this->field);
        $attribute = $this->parseAttributeName($field);
      
        $validationParams = $this->parseParameters($parameters);
        $validationResult = $this->validateJsRemoteRequest($attribute, $validationParams);
        return $validationResult;
    }

    /**
     * Throw the failed validation exception.
     *
     * @param mixed $result
     * @param CValidation_Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException|\Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function throwValidationException($result, $validator) {
        if($result===true) {
            echo json_encode($result);
        } else {
            echo $validator->errors()->first();
        }
       
    }

   
    /**
     *  Parse Validation input request data.
     *
     * @param $data
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
     * @return array|bool
     */
    protected function validateJsRemoteRequest($attribute, $parameters) {
        $this->setRemoteValidation($attribute, $parameters['validate_all']);
        $validator = $this->validator;
        if ($validator->passes()) {
            return true;
        }
        return $validator->messages()->get($attribute);
    }

    /**
     * Sets data for validate remote rules.
     *
     * @param $attribute
     * @param bool $validateAll
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
     * @param BaseValidator $validator
     * @return mixed
     */
    protected function purgeNonRemoteRules($rules, $validator) {
        $protectedValidator = $this->createProtectedCaller($validator);
        foreach ($rules as $i => $rule) {
            $parsedRule = CValidation_RuleParser::parse([$rule]);
            if (!$this->isRemoteRule($parsedRule[0])) {
                unset($rules[$i]);
            }
        }
        return $rules;
    }

}
