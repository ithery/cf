<?php

trait CController_Trait_ValidateRequestTrait {
    /**
     * Run the validation routine against the given validator.
     *
     * @param \CValidation_Validator|array $validator
     * @param null|\CHTTP_Request          $request
     *
     * @throws \CValidation_Exception
     *
     * @return array
     */
    public function validateWith($validator, CHTTP_Request $request = null) {
        $request = $request ?: c::request();

        if (is_array($validator)) {
            $validator = $this->getValidationFactory()->make($request->all(), $validator);
        }

        return $validator->validate();
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param \CHTTP_Request|array $request
     * @param array                $rules
     * @param array                $messages
     * @param array                $customAttributes
     *
     * @throws \CValidation_Exception
     *
     * @return array
     */
    public function validate(
        $request,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
        return $this->getValidationFactory()->make(
            $request instanceof CHTTP_Request ? $request->all() : $request,
            $rules,
            $messages,
            $customAttributes
        )->validate();
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param string               $errorBag
     * @param \CHTTP_Request|array $request
     * @param array                $rules
     * @param array                $messages
     * @param array                $customAttributes
     *
     * @throws \CValidation_Exception
     *
     * @return array
     */
    public function validateWithBag(
        $errorBag,
        $request,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
        try {
            return $this->validate($request, $rules, $messages, $customAttributes);
        } catch (CValidation_Exception $e) {
            $e->errorBag = $errorBag;

            throw $e;
        }
    }

    /**
     * Get a validation factory instance.
     *
     * @return \CValidation_Factory
     */
    protected function getValidationFactory() {
        return CValidation::factory();
    }
}
