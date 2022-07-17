<?php

interface CValidation_Contract_ValidatorAwareRuleInterface {
    /**
     * Set the current validator.
     *
     * @param \CValidation_Validator $validator
     *
     * @return $this
     */
    public function setValidator($validator);
}
