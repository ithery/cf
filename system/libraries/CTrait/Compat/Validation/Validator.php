<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 14, 2019, 11:22:25 AM
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
