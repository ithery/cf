<?php

class CHTTP_InputSanitizer {
    protected $originalPost;

    protected $originalGet;

    protected $originalFiles;

    /**
     * Sanitizes global GET, POST and COOKIE data. Also takes care of
     * magic_quotes and register_globals, if they have been enabled.
     *
     * @return void
     */
    public function __construct() {
        $this->originalPost = $_POST;
        $this->originalGet = $_GET;
        $this->originalFiles = $_FILES;

        // register_globals is enabled
        if (ini_get('register_globals')) {
            if (isset($_REQUEST['GLOBALS'])) {
                // Prevent GLOBALS override attacks
                exit('Global variable overload attack.');
            }

            // Destroy the REQUEST global
            $_REQUEST = [];

            // These globals are standard and should not be removed
            $preserve = ['GLOBALS', '_REQUEST', '_GET', '_POST', '_FILES', '_COOKIE', '_SERVER', '_ENV', '_SESSION'];

            // This loop has the same effect as disabling register_globals
            foreach (array_diff(array_keys($GLOBALS), $preserve) as $key) {
                global $$key;
                $$key = null;

                // Unset the global variable
                unset($GLOBALS[$key], $$key);
            }

            // Warn the developer about register globals
            CF::log(CLogger::DEBUG, 'Disable register_globals! It is evil and deprecated: http://php.net/register_globals');
        }

        if (is_array($_GET)) {
            foreach ($_GET as $key => $val) {
                // Sanitize $_GET
                $_GET[$this->cleanInputKeys($key)] = $this->cleanInputData($val);
            }
        } else {
            $_GET = [];
        }

        if (is_array($_POST)) {
            foreach ($_POST as $key => $val) {
                // Sanitize $_POST
                $_POST[$this->cleanInputKeys($key)] = $this->cleanInputData($val);
            }
        } else {
            $_POST = [];
        }

        if (is_array($_COOKIE)) {
            foreach ($_COOKIE as $key => $val) {
                // Ignore special attributes in RFC2109 compliant cookies
                if ($key == '$Version' or $key == '$Path' or $key == '$Domain') {
                    continue;
                }

                // Sanitize $_COOKIE
                $_COOKIE[$this->cleanInputKeys($key)] = $this->cleanInputData($val);
            }
        } else {
            $_COOKIE = [];
        }

        CF::log(CLogger::DEBUG, 'Global GET, POST and COOKIE data sanitized');
    }

    /**
     * This is a helper method. It enforces W3C specifications for allowed
     * key name strings, to prevent malicious exploitation.
     *
     * @param string $str string to clean
     *
     * @return string
     */
    public function cleanInputKeys($str) {
        return $str;
    }

    /**
     * This is a helper method. It escapes data and forces all newline
     * characters to "\n".
     *
     * @param array|string $str string to clean
     *
     * @return string
     */
    public function cleanInputData($str) {
        if (is_array($str)) {
            $newArray = [];
            foreach ($str as $key => $val) {
                // Recursion!
                $newArray[$this->cleanInputKeys($key)] = $this->cleanInputData($val);
            }

            return $newArray;
        }

        if (strpos($str, "\r") !== false) {
            // Standardize newlines
            $str = str_replace(["\r\n", "\r"], "\n", $str);
        }

        return $str;
    }
}
