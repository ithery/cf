<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * crequest helper class.
 */
class crequest {

    // Possible HTTP methods
    protected static $http_methods = array('get', 'head', 'options', 'post',
        'put', 'delete');
    // Content types from client's HTTP Accept request header (array)
    protected static $accept_types;

    /**
     * Returns the HTTP referrer, or the default if the referrer is not set.
     *
     * @param   mixed   default to return
     * @return  string
     */
    public static function referrer($default = FALSE) {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            // Set referrer
            $ref = $_SERVER['HTTP_REFERER'];

            if (strpos($ref, curl::base(FALSE)) === 0) {
                // Remove the base URL from the referrer
                $ref = substr($ref, strlen(curl::base(FALSE)));
            }
        }

        return isset($ref) ? $ref : $default;
    }

    public static function current_container_id() {
        return carr::get($_GET, 'capp_current_container_id');
    }

    public static function user_agent() {
        return CF::user_agent();
    }

    public static function browser() {
        return "";
        $browser = CBrowser::instance()->browser();
        return $browser->Browser;
    }

    public static function browser_version() {
        return "";
        $browser = CBrowser::instance()->browser();
        return $browser->Version;
    }

    public static function browser_major_version() {
        return "";
        $browser = CBrowser::instance()->browser();
        return $browser->MajorVer;
    }

    public static function browser_minor_version() {
        return "";
        $browser = CBrowser::instance()->browser();
        return $browser->MinorVer;
    }

    public static function platform() {
        return "";
        $browser = CBrowser::instance()->browser();
        return $browser->Platform;
    }

    public static function platform_version() {
        return "";
        $browser = CBrowser::instance()->browser();
        return $browser->Platform_Version;
    }

    /*
      public static function browser_name($browser = "") {
      if(strlen($browser)==0) {
      $browser = crequest::browser();
      }
      $browser_name = "Unknown";
      switch($browser) {
      case "firefox": $browser_name="Firefox"; break;
      case "ie": $browser_name="Internet Explorer"; break;
      case "ipad": $browser_name="IPad"; break;
      case "android": $browser_name="Android"; break;
      case "chrome": $browser_name="Chrome"; break;
      case "safari": $browser_name="Safari"; break;
      case "air": $browser_name="Air"; break;
      case "fluid": $browser_name="Fluid"; break;

      }



      return $browser_name;
      }
     */

    /**
     * Fetch the Remote Address.
     *
     * @return string
     */
    public static function remote_address() {
        // Server keys that could contain the client IP address
        $keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');
        $ip_address = "";
        foreach ($keys as $key) {
            if (isset($_SERVER[$key])) {
                $ip_address = $_SERVER[$key];
                // An IP address has been found
                break;
            }
        }

        if ($comma = strrpos($ip_address, ',') !== FALSE) {
            $ip_address = substr($ip_address, $comma + 1);
        }

        if (!cvalid::ip($ip_address)) {
            // Use an empty IP
            $ip_address = '0.0.0.0';
        }

        return $ip_address;
    }

    /**
     * Returns the current request protocol, based on $_SERVER['https']. In CLI
     * mode, NULL will be returned.
     *
     * @return  string
     */
    public static function protocol() {
        if (PHP_SAPI === 'cli') {
            return NULL;
        } elseif (!empty($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] === 'on') {
            return 'https';
        } else {
            return 'http';
        }
    }

    /**
     * Tests if the current request is an AJAX request by checking the X-Requested-With HTTP
     * request header that most popular JS frameworks now set for AJAX calls.
     *
     * @return  boolean
     */
    public static function is_ajax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
     * Checks to see if the page is being server over SSL or not
     *
     * @return  bool
     *
     * @access  public
     * @since   1.0.000
     * @static
     */
    public static function is_https() {
        if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Returns current request method.
     *
     * @throws  CF_Exception in case of an unknown request method
     * @return  string
     */
    public static function method() {
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if (!in_array($method, request::$http_methods))
            throw new CF_Exception('request.unknown_method', $method);

        return $method;
    }

    /**
     * Returns boolean of whether client accepts content type.
     *
     * @param   string   content type
     * @param   boolean  set to TRUE to disable wildcard checking
     * @return  boolean
     */
    public static function accepts($type = NULL, $explicit_check = FALSE) {
        crequest::parse_accept_header();

        if ($type === NULL)
            return crequest::$accept_types;

        return (crequest::accepts_at_quality($type, $explicit_check) > 0);
    }

    /**
     * Compare the q values for given array of content types and return the one with the highest value.
     * If items are found to have the same q value, the first one encountered in the given array wins.
     * If all items in the given array have a q value of 0, FALSE is returned.
     *
     * @param   array    content types
     * @param   boolean  set to TRUE to disable wildcard checking
     * @return  mixed    string mime type with highest q value, FALSE if none of the given types are accepted
     */
    public static function preferred_accept($types, $explicit_check = FALSE) {
        // Initialize
        $mime_types = array();
        $max_q = 0;
        $preferred = FALSE;

        // Load q values for all given content types
        foreach (array_unique($types) as $type) {
            $mime_types[$type] = crequest::accepts_at_quality($type, $explicit_check);
        }

        // Look for the highest q value
        foreach ($mime_types as $type => $q) {
            if ($q > $max_q) {
                $max_q = $q;
                $preferred = $type;
            }
        }

        return $preferred;
    }

    /**
     * Returns quality factor at which the client accepts content type.
     *
     * @param   string   content type (e.g. "image/jpg", "jpg")
     * @param   boolean  set to TRUE to disable wildcard checking
     * @return  integer|float
     */
    public static function accepts_at_quality($type = NULL, $explicit_check = FALSE) {
        crequest::parse_accept_header();

        // Normalize type
        $type = strtolower((string) $type);

        // General content type (e.g. "jpg")
        if (strpos($type, '/') === FALSE) {
            // Don't accept anything by default
            $q = 0;

            // Look up relevant mime types
            foreach ((array) CF::config('mimes.' . $type) as $type) {
                $q2 = crequest::accepts_at_quality($type, $explicit_check);
                $q = ($q2 > $q) ? $q2 : $q;
            }

            return $q;
        }

        // Content type with subtype given (e.g. "image/jpg")
        $type = explode('/', $type, 2);

        // Exact match
        if (isset(crequest::$accept_types[$type[0]][$type[1]]))
            return crequest::$accept_types[$type[0]][$type[1]];

        // Wildcard match (if not checking explicitly)
        if ($explicit_check === FALSE AND isset(request::$accept_types[$type[0]]['*']))
            return crequest::$accept_types[$type[0]]['*'];

        // Catch-all wildcard match (if not checking explicitly)
        if ($explicit_check === FALSE AND isset(request::$accept_types['*']['*']))
            return crequest::$accept_types['*']['*'];

        // Content type not accepted
        return 0;
    }

    /**
     * Parses client's HTTP Accept request header, and builds array structure representing it.
     *
     * @return  void
     */
    protected static function parse_accept_header() {
        // Run this function just once
        if (crequest::$accept_types !== NULL)
            return;

        // Initialize accept_types array
        crequest::$accept_types = array();

        // No HTTP Accept header found
        if (empty($_SERVER['HTTP_ACCEPT'])) {
            // Accept everything
            crequest::$accept_types['*']['*'] = 1;
            return;
        }

        // Remove linebreaks and parse the HTTP Accept header
        foreach (explode(',', str_replace(array("\r", "\n"), '', $_SERVER['HTTP_ACCEPT'])) as $accept_entry) {
            // Explode each entry in content type and possible quality factor
            $accept_entry = explode(';', trim($accept_entry), 2);

            // Explode each content type (e.g. "text/html")
            $type = explode('/', $accept_entry[0], 2);

            // Skip invalid content types
            if (!isset($type[1]))
                continue;

            // Assume a default quality factor of 1 if no custom q value found
            $q = (isset($accept_entry[1]) AND preg_match('~\bq\s*+=\s*+([.0-9]+)~', $accept_entry[1], $match)) ? (float) $match[1] : 1;

            // Populate accept_types array
            if (!isset(crequest::$accept_types[$type[0]][$type[1]]) OR $q > request::$accept_types[$type[0]][$type[1]]) {
                crequest::$accept_types[$type[0]][$type[1]] = $q;
            }
        }
    }

}

// End request