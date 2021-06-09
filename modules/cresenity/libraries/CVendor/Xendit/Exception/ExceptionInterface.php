<?php
interface CVendor_Xendit_Exception_ExceptionInterface extends \Throwable {
    /**
     * Get error code for the exception instance
     *
     * @return string
     */
    public function getErrorCode();
}
