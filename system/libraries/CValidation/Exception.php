<?php

defined('SYSPATH') OR die('No direct script access.');

class CValidation_Exception extends CException {

    /**
     * @var  object  Validation instance
     */
    public $array;

    /**
     * @param  Validation   $array      Validation object
     * @param  string       $message    error message
     * @param  array        $values     translation variables
     * @param  int          $code       the exception code
     */
    public function __construct(CValidation $array, $message = 'Failed to validate array', array $values = NULL, $code = 0, Exception $previous = NULL) {
        $this->array = $array;

        parent::__construct($message, $values, $code, $previous);
    }

}
