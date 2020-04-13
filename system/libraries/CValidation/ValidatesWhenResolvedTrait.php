<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Provides default implementation of ValidatesWhenResolved contract.
 */
trait CValidation_ValidatesWhenResolvedTrait {

    /**
     * Validate the class instance.
     *
     * @return void
     */
    public function validateResolved() {
        $this->prepareForValidation();

        if (!$this->passesAuthorization()) {
            $this->failedAuthorization();
        }

        $instance = $this->getValidatorInstance();

        if (!$instance->passes()) {
            $this->failedValidation($instance);
        }
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation() {
        // no default action
    }

    /**
     * Get the validator instance for the request.
     *
     * @return CValidation_Validator
     */
    protected function getValidatorInstance() {
        return $this->validator();
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  CValidation_Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator) {
        throw new ValidationException($validator);
    }

    /**
     * Determine if the request passes the authorization check.
     *
     * @return bool
     */
    protected function passesAuthorization() {
        if (method_exists($this, 'authorize')) {
            return $this->authorize();
        }

        return true;
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\UnauthorizedException
     */
    protected function failedAuthorization() {
        throw new UnauthorizedException;
    }

}
