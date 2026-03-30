<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CValidation_Validator
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Validation_Validator {
    /**
     * @deprecated 1.2
     */
    public function first_error() {
        return $this->errors()->first();
    }

    public function rule($attributeName, $rule, $bind = null, $customMessage = null) {
        $rules = $this->getRules();
        if ($rule == 'not_empty') {
            $rule = 'required';
        }

        if (isset($rules[$attributeName])) {
            if (is_array($rules[$attributeName])) {
                $rules[$attributeName][] = $rule;
            } else {
                $rules[$attributeName] .= '|' . $rule;
            }
        } else {
            $rules[$attributeName] = $rule;
        }
        $this->setRules($rules);
        $this->customMessages[$attributeName . '.' . $rule] = $customMessage;

        return $this;
    }
}
