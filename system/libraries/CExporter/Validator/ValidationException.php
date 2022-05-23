<?php

class CExporter_Validator_ValidationException extends CValidation_Exception {
    /**
     * @var Failure[]
     */
    protected $failures;

    /**
     * @param CValidation_Exception $previous
     * @param array                 $failures
     */
    public function __construct(CValidation_Exception $previous, array $failures) {
        parent::__construct($previous->validator, $previous->response, $previous->errorBag);
        $this->failures = $failures;
    }

    /**
     * @return string[]
     */
    public function errors() {
        return c::collect($this->failures)->map->toArray()->all();
    }

    /**
     * @return array
     */
    public function failures() {
        return $this->failures;
    }
}
