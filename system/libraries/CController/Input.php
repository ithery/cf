<?php

class CController_Input {
    // Input singleton
    protected static $instance;

    // Enable or disable automatic XSS cleaning
    protected $useXssClean = true;

    protected $originalPost;

    protected $originalGet;

    protected $originalFiles;

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

    public function originalGetData() {
        return $this->originalGet;
    }

    public function originalPostData() {
        return $this->originalPost;
    }

    public function originalFilesData() {
        return $this->originalPost;
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

        if ($this->useXssClean === false and $xssClean === true) {
            // XSS clean the value
            $value = $this->xssClean($value);
        }

        return $value;
    }

    /**
     * Clean cross site scripting exploits from string.
     * HTMLPurifier may be used if installed, otherwise defaults to built in method.
     * Note - This function should only be used to deal with data upon submission.
     * It's not something that should be used for general runtime processing
     * since it requires a fair amount of processing overhead.
     *
     * @param string|array $data data to clean
     *
     * @return string}array
     */
    public function xssClean($data) {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $data[$key] = $this->xssClean($val);
            }

            return $data;
        }

        // Do not clean empty strings
        if (trim($data) === '') {
            return $data;
        }

        require_once DOCROOT . 'system/vendor/HTMLPurifier.auto.php';

        $config = HTMLPurifier_Config::createDefault();
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true, 'data' => true]);

        $def = $config->getHTMLDefinition(true);
        $def->addAttribute('span', 'data-member-id', 'Number');
        $def->addAttribute('img', 'style', 'Text');

        $purifier = new HTMLPurifier($config);
        $data = $purifier->purify($data);

        return $data;
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
            $new_array = [];
            foreach ($str as $key => $val) {
                // Recursion!
                $new_array[$this->cleanInputKeys($key)] = $this->cleanInputData($val);
            }
            return $new_array;
        }

        if ($this->useXssClean === true) {
            $str = $this->xssClean($str);
        }

        if (strpos($str, "\r") !== false) {
            // Standardize newlines
            $str = str_replace(["\r\n", "\r"], "\n", $str);
        }

        return $str;
    }
}
