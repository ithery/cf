<?php

/**
 * Holds validation rules for a single form field.
 *
 * Rules are normalized to the Laravel-style rule strings (eg. 'required', 'min:5')
 * used by {@see CElement_Component_Form_Validation} / {@see CValidation}, so field-level
 * validation declared via addValidation() can be collected by CElement_Component_Form
 * and validated through the same pipeline as Form::setValidation().
 */
class CElement_Component_Form_FieldValidation {
    use CTrait_Compat_Element_Form_FieldValidation;

    /**
     * @var array
     */
    private $rules;

    public function __construct() {
        $this->rules = [];
    }

    public static function factory() {
        return new CElement_Component_Form_FieldValidation();
    }

    /**
     * @param string      $name
     * @param mixed       $param
     *
     * @return $this
     */
    public function addValidation($name, $param = '') {
        $rule = $this->normalizeRule($name, $param);
        if ($rule !== null && !in_array($rule, $this->rules, true)) {
            $this->rules[] = $rule;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function rules() {
        return $this->rules;
    }

    /**
     * Normalize a rule name/param pair into a Laravel-style rule string.
     *
     * Supports both the new single-argument style, eg. addValidation('required'),
     * addValidation('min:5'), and the legacy two-argument style,
     * eg. addValidation('min', 5), addValidation('condrequired', 'other_field').
     *
     * @param mixed $name
     * @param mixed $param
     *
     * @return string|null
     */
    private function normalizeRule($name, $param) {
        if ($name === null || $name === '') {
            return null;
        }
        if ($param === null || (is_scalar($param) && strlen((string) $param) == 0)) {
            //single-argument call, $name is the rule itself
            return $name;
        }

        switch (strtolower($name)) {
            case 'condrequired':
                return 'required_with:' . $param;
            case 'custom':
                return (string) $param;
            case 'equals':
                return 'same:' . $param;
            case 'notequals':
                return 'different:' . $param;
            default:
                return $name . ':' . $param;
        }
    }

    /**
     * @deprecated no longer renders anything, validation is now driven by Form::setValidation()
     *
     * @return string
     */
    public function validationClass() {
        return '';
    }
}
