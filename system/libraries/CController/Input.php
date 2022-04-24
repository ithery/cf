<?php

class CController_Input {
    /**
     * Input singleton.
     *
     * @var CController_Input
     */
    protected static $instance;

    /**
     * Enable or disable automatic XSS cleaning.
     *
     * @var bool
     */
    protected $useXssClean = true;

    /**
     * Retrieve a singleton instance of Input. This will always be the first
     * created instance of this class.
     *
     * @return object
     */
    public static function instance() {
        if (static::$instance === null) {
            // Create a new instance
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Sanitizes global GET, POST and COOKIE data. Also takes care of
     * magic_quotes and register_globals, if they have been enabled.
     *
     * @return void
     */
    private function __construct() {
    }

    /**
     * Fetch an item from the $_GET array.
     *
     * @param string     $key      key to find
     * @param null|mixed $default  default value
     * @param bool       $xssClean XSS clean the value
     *
     * @return mixed
     */
    public function get($key = [], $default = null, $xssClean = false) {
        return $this->searchArray($_GET, $key, $default, $xssClean);
    }

    /**
     * Fetch an item from the $_POST array.
     *
     * @param string     $key      key to find
     * @param null|mixed $default  default value
     * @param bool       $xssClean XSS clean the value
     *
     * @return mixed
     */
    public function post($key = [], $default = null, $xssClean = false) {
        return $this->searchArray($_POST, $key, $default, $xssClean);
    }

    /**
     * Fetch an item from the $_COOKIE array.
     *
     * @param string     $key       key to find
     * @param null|mixed $default   default value
     * @param bool       $xss_clean XSS clean the value
     *
     * @return mixed
     */
    public function cookie($key = [], $default = null, $xss_clean = false) {
        return $this->searchArray($_COOKIE, $key, $default, $xss_clean);
    }

    /**
     * Fetch an item from the $_SERVER array.
     *
     * @param string     $key      key to find
     * @param null|mixed $default  default value
     * @param bool       $xssClean XSS clean the value
     *
     * @return mixed
     */
    public function server($key = [], $default = null, $xssClean = false) {
        return $this->searchArray($_SERVER, $key, $default, $xssClean);
    }

    /**
     * Fetch an item from a global array.
     *
     * @param array      $array    array to search
     * @param string     $key      key to find
     * @param null|mixed $default  default value
     * @param bool       $xssClean XSS clean the value
     *
     * @return mixed
     */
    protected function searchArray($array, $key, $default = null, $xssClean = false) {
        if ($key === []) {
            return $array;
        }

        if (!isset($array[$key])) {
            return $default;
        }

        // Get the value
        $value = $array[$key];

        return $value;
    }

    /**
     * @return string
     *
     * @deprecated 1.2
     */
    // @codingStandardsIgnoreStart
    public function ip_address() {
        return CHTTP::request()->ip();
    }
}
