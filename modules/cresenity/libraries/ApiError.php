<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Aug 4, 2015
     * @license http://piposystem.com Piposystem
     */
    class ApiError implements ArrayAccess, Iterator, Countable {
        
        protected $errors;
        protected $error_list;
        protected $err_code;
        protected $err_message;
        public static $instance;
        
        private function __construct() {
            $this->err_code = 0;
            $this->err_message = "";
        }
        
        public static function instance(){
            if (self::$instance == null) {
                self::$instance = new ApiError();
            }
            return self::$instance;
        }
        
//        public function set_error($err_code, $custom_message = "", $replace = null){
//            $def_message = $this->error_list[$err_code];
//            if ($replace != NULL) {
//                $def_message = str_replace(":" .$replace['search'], $replace['replace'], $def_message);
//            }
//            $this->add($def_message .$custom_message, $err_code);
//            return $this;
//        }
        
        public function add_default($err_code){
            $err_message = carr::get($this->error_list, $err_code);
            $this->add($err_message, $err_code);
        }
        
        public function add($err_message, $err_code = "9999"){
            $this->err_code = $err_code;
            $this->err_message = $err_message;
            if (is_array($err_message)){
                foreach ($err_message as $key => $value) {
                    $this->errors[] = $value;
                }
            }
            else {
                $this->errors[] = $err_message;
            }
            return $this;
        }
        
        public function get_errors(){
            return $this->errors;
        }
        
        public function code(){
            return $this->err_code;
        }
        
        public function get_err_message(){
            return $this->err_message;
        }
        
        public function get_error_list(){
            return $this->error_list;
        }
        
        public function set_error_list($error_list){
            $this->error_list = $error_list;
            return $this;
        }
        
        public function render() {
            $html = "";
            foreach ($this->errors as $error) {
                if ($html != "")
                    $html .= PHP_EOL;
                $html .= "" . $error . "";
            }
            return $html;
        }
        
        public function count() {
            return count($this->errors);
        }

        public function current() {
            return $this->offsetGet($this->err_code);
        }

        public function key() {
            return $this->err_code;
        }

        public function next() {
            ++$this->err_code;
            return $this;
        }
        
        public function prev() {
            --$this->err_code;
            return $this;
        }

        public function offsetExists($offset) {
            return isset($this->errors[$offset]);
        }

        public function offsetGet($offset) {
            if ($this->offsetExists($offset))
                return $this->errors[$offset];
            return FALSE;
        }

        public function offsetSet($offset, $value) {
            $this->errors[$offset] = $value;
        }

        public function offsetUnset($offset) {
            unset($this->errors[$offset]);
        }

        public function rewind() {
            $this->err_code = 0;
            return $this;
        }

        public function valid() {
            return $this->offsetExists($this->err_code);
        }

    }
    