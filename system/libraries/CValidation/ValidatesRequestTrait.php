<?php
trait CValidation_ValidatesRequestTrait {
    /**
     * Run the validation routine against the given validator.
     *
     * @param CValidation_Validator|array $validator
     * @param CHTTP_Request|null          $request
     *
     * @return array
     *
     * @throws CValidation_Exception
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
     * @param CHTTP_Request $request
     * @param array         $rules
     * @param array         $messages
     * @param array         $customAttributes
     *
     * @return array
     *
     * @throws CValidation_Exception
     */
    public function validate(
        CHTTP_Request $request,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
        return $this->getValidationFactory()->make(
            $request->all(),
            $rules,
            $messages,
            $customAttributes
        )->validate();
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param string        $errorBag
     * @param CHTTP_Request $request
     * @param array         $rules
     * @param array         $messages
     * @param array         $customAttributes
     *
     * @return array
     *
     * @throws CValidation_Exception
     */
    public function validateWithBag(
        $errorBag,
        CHTTP_Request $request,
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
     * @return CValidation_Factory
     */
    protected function getValidationFactory() {
        return CValidation_Factory::instance();
    }
}
