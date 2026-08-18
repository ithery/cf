<?php

/**
 * Interface ValidatorInterface.
 */
interface CModel_Validator_Contract_ValidatorInterface {
    const RULE_CREATE = 'create';

    const RULE_UPDATE = 'update';

    /**
     * Set Id.
     *
     * @param $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * With.
     *
     * @param array
     *
     * @return $this
     */
    public function with(array $input);

    /**
     * Pass the data and the rules to the validator.
     *
     * @param string $action
     *
     * @return bool
     */
    public function passes($action = null);

    /**
     * Pass the data and the rules to the validator or throws ValidatorException.
     *
     * @param string $action
     *
     * @throws CModel_Validator_Exception_ValidatorException
     *
     * @return bool
     */
    public function passesOrFail($action = null);

    /**
     * Errors.
     *
     * @return array
     */
    public function errors();

    /**
     * Errors.
     *
     * @return CBase_MessageBag
     */
    public function errorsBag();

    /**
     * Set Rules for Validation.
     *
     * @param array $rules
     *
     * @return $this
     */
    public function setRules(array $rules);

    /**
     * Get rule for validation by action ValidatorInterface::RULE_CREATE or ValidatorInterface::RULE_UPDATE.
     *
     * Default rule: CModel_Validator_Contract_ValidatorInterface::RULE_CREATE
     *
     * @param $action
     *
     * @return array
     */
    public function getRules($action = null);
}
