<?php

interface CValidation_Contract_DataAwareRuleInterface {
    /**
     * Set the data under validation.
     *
     * @param array $data
     *
     * @return $this
     */
    public function setData($data);
}
