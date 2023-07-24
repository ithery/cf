<?php

defined('SYSPATH') or die('No direct access allowed.');

class CCookie {
    public static function jar() {
        return new CCookie_Jar();
    }

    /**
     * Sets a cookie with the given parameters.
     *
     * @param string|array $name     cookie name or array of config options
     * @param string       $value    cookie value
     * @param int          $expire   number of seconds before the cookie expires
     * @param string       $path     URL path to allow
     * @param string       $domain   URL domain to allow
     * @param bool         $secure   HTTPS only
     * @param bool         $httponly HTTP only (requires PHP 5.2 or higher)
     *
     * @return bool
     */
    public static function set($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null) {
        if (headers_sent()) {
            return false;
        }

        // If the name param is an array, we import it
        is_array($name) and extract($name, EXTR_OVERWRITE);

        // Fetch default options
        $config = CF::config('cookie');

        foreach (['value', 'expire', 'domain', 'path', 'secure', 'httponly'] as $item) {
            if ($$item === null and isset($config[$item])) {
                $$item = $config[$item];
            }
        }

        // Expiration timestamp
        $expire = ($expire == 0) ? 0 : time() + (int) $expire;

        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
     * Fetch a cookie value, using the Input library.
     *
     * @param string $name      cookie name
     * @param mixed  $default   default value
     * @param bool   $xss_clean use XSS cleaning on the value
     *
     * @return string
     */
    public static function get($name, $default = null, $xss_clean = false) {
        return CController_Input::instance()->cookie($name, $default, $xss_clean);
    }

    /**
     * Nullify and unset a cookie.
     *
     * @param string $name   cookie name
     * @param string $path   URL path
     * @param string $domain URL domain
     *
     * @return bool
     */
    public static function delete($name, $path = null, $domain = null) {
        if (!isset($_COOKIE[$name])) {
            return false;
        }

        // Delete the cookie from globals
        unset($_COOKIE[$name]);

        // Sets the cookie value to an empty string, and the expiration to 24 hours ago
        return static::set($name, '', -86400, $path, $domain, false, false);
    }
}
