<?php

class CVendor_Xendit_Exception_ApiException extends \Exception implements CVendor_Xendit_Exception_ExceptionInterface {
    protected $errorCode;

    /**
     * Get error code for the exception instance
     *
     * @return string
     */
    public function getErrorCode() {
        return $this->errorCode;
    }

    /**
     * Create new instance of ApiException
     *
     * @param string $message   corresponds to message field in Xendit's HTTP error
     * @param string $code      corresponds to http status in Xendit's HTTP response
     * @param string $errorCode corresponds to error_code field in Xendit's HTTP
     *                          error
     */
    public function __construct($message, $code, $errorCode) {
        if (!$message) {
            throw new $this('Unknown ' . get_class($this));
        }
        parent::__construct($message, $code);
        $this->errorCode = $errorCode;
    }
}
