<?php

class HTTP_InputSanitizer {
    /**
     * Sanitizes global GET, POST and COOKIE data. Also takes care of
     * magic_quotes and register_globals, if they have been enabled.
     *
     * @return void
     */
    public function __construct() {
        // Use XSS clean?
        $this->use_xss_clean = (bool) CF::$global_xss_filtering;
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
                $_GET[$this->clean_input_keys($key)] = $this->clean_input_data($val);
            }
        } else {
            $_GET = [];
        }

        if (is_array($_POST)) {
            foreach ($_POST as $key => $val) {
                // Sanitize $_POST
                $_POST[$this->clean_input_keys($key)] = $this->clean_input_data($val);
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
                $_COOKIE[$this->clean_input_keys($key)] = $this->clean_input_data($val);
            }
        } else {
            $_COOKIE = [];
        }

        // Create a singleton
        Input::$instance = $this;

        CF::log(CLogger::DEBUG, 'Global GET, POST and COOKIE data sanitized');
    }
}
