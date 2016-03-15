<?php

    /**
     * 
     * __construct
     * @param string    $message
     * @param array     $variable
     * @param int       $code
     * @param Helpers_Api_Validation_Exception $previous
     */
    class Helpers_Validation_Api_Exception extends Helpers_Validation_Exception {

        public static $php_errors = array();

        /**
         * 
         * @param string    $message
         * @param array     $variable
         * @param int       $code
         * @param Helpers_Api_Validation_Exception $previous
         */
        public function __construct($message = "", $code = 0, array $variable = NULL, Exception $previous = NULL) {
            parent::__construct($message, (int) $code, $previous);
            $this->code = $code;            
        }

        public function getArrayMessage() {
            return Helpers_Validation_Rules::$error_collection;
        }

        public function __toString() {
            return Helpers_Validation_Api_Exception::text($this);
        }

        public static function text(Exception $e) {
//            return sprintf('%s [ %s ]: %s ~ %s [ %d ]', get_class($e), $e->getCode(), strip_tags($e->getMessage()), Pipo_Debug::path($e->getFile()), $e->getLine());
        }

    }

    