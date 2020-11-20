<?php

/**
 * Description of CFDeprecatedTrait
 *
 * @author Hery
 */
trait CFDeprecatedTrait {

    private static $write_cache;

    public static function doDeprecated() {
        if (class_exists('cdbg')) {
            cdbg::deprecated();
        }
    }

    /**
     * 
     * @deprecated
     * @param string $directory
     * @param string $domain
     * @return array array of directory
     */
    public static function get_dirs($directory, $domain = null) {
        return self::getDirs($directory, $domain);
    }

    /**
     * 
     * @deprecated
     * @param string $directory
     * @param string $domain
     * @return string|null directory
     */
    public static function get_dir($directory = '', $domain = null) {
        return static::getDir($directory, $domain);
    }

    /**
     * @deprecated Please use getFiles
     * @param string $directory
     * @param string $filename
     * @param string $domain
     * @return string[]
     */
    public static function get_files($directory, $filename, $domain = null) {
        return self::getFiles($directory, $filename, $domain);
    }

    /**
     * 
     * @deprecated
     * @param string $directory
     * @param string $filename
     * @param string $domain
     * @return string
     */
    public static function get_file($directory, $filename, $domain = null) {
        return self::getFile($directory, $filename, $domain);
    }

    /**
     * get path with theme (theme is not supported again, dont use this function)
     * @deprecated
     * @param type $process
     * @return type
     */
    public static function include_paths_theme($process = FALSE) {

        return self::include_paths($process, true);
    }

    /**
     * 
     * @param type $filename
     * @param type $domain
     * @return type
     * @deprecated
     */
    public static function get_config($filename, $domain = null) {
        $files = self::getFiles('config', $filename, $domain);
        $files = array_reverse($files);
        $ret = array();
        foreach ($files as $file) {
            $cfg = include $file;
            $ret = array_merge($ret, $cfg);
        }
        return $ret;
    }

    /**
     * Get all include paths. APPPATH is the first path, followed by module
     * paths in the order they are configured, follow by the SYSPATH.
     *
     * @deprecated
     * @param   boolean  re-process the include paths
     * @return  array
     * @see CF::paths
     */
    public static function include_paths($process = FALSE, $with_theme = false) {
        return self::paths();
    }

    /**
     * Add Shared App in runtime
     * This function is deprecated, use CF::addSharedApp
     * 
     * @deprecated
     * @param string $app_code
     */
    public static function add_shared_app_code($app_code) {
        return self::addSharedApp($app_code);
    }

    /**
     * Get application id for domain
     * This function is deprecated, use CF::appId
     *
     * @return  string
     * @deprecated
     */
    public static function app_id($domain = null) {
        return self::appId($domain);
    }

    /**
     * Get application code for domain
     * This function is deprecated, use CF::appCode
     * 
     * @deprecated
     * @return  string
     */
    public static function app_code($domain = null) {
        return self::appCode($domain);
    }

    /**
     * Get org id for domain
     * This function is deprecated, use CF::orgId
     * 
     * @deprecated
     * @param string $domain
     * @return int
     */
    public static function org_id($domain = null) {
        return self::orgId($domain);
    }

    /**
     * Get org code for this domain
     * This function is deprecated, use CF::orgCode
     *
     * @deprecated
     * @param string $domain
     * @return string
     */
    public static function org_code($domain = null) {
        return self::orgCode($domain);
    }

    /**
     * Returns all traits used by a class, its subclasses and trait of their traits.
     *
     * @param  object|string  $class
     * @return array
     * @deprecated
     */
    public static function class_uses_recursive($class) {
        return static::classUsesRecursive($class);
    }

    /**
     * Returns all traits used by a trait and its traits.
     *
     * @param  string  $trait
     * @return array
     * @deprecated
     */
    public static function trait_uses_recursive($trait) {
        return static::traitUsesRecursive($trait);
    }

    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object  $class
     * @return string
     * @deprecated
     */
    public static function class_basename($class) {
        return static::classBasename($class);
    }

    /**
     * Displays a 404 page.
     *
     * @throws  C_404_Exception
     * @param   string  URI of page
     * @param   string  custom template
     * @return  void
     * @deprecated
     */
    public static function show_404($page = FALSE, $template = FALSE) {
        return self::show404($page, $template);
    }

    /**
     * @deprecated
     * @param type $directory
     * @param type $filename
     * @param type $required
     * @param type $ext
     * @return type
     */
    public static function find_file($directory, $filename, $required = FALSE, $ext = FALSE) {
        return static::findFile($directory, $filename, $required, $ext);
    }

    /**
     * Dual-purpose PHP error and exception handler. Uses the kohana_error_page
     * view to display the message.
     *
     * @param   integer|object  exception object or error code
     * @param   string          error message
     * @param   string          filename
     * @param   integer         line number
     * @return  void
     * @deprecated
     */
    public static function exception_handler($exception, $message = NULL, $file = NULL, $line = NULL) {
        return CF::exceptionHandler($exception, $message, $file, $line);
    }

    /**
     * Closes all open output buffers, either by flushing or cleaning, and stores the C
     * output buffer for display during shutdown.
     *
     * @param   boolean  disable to clear buffers, rather than flushing
     * @return  void
     * @deprecated
     */
    public static function close_buffers($flush = TRUE) {
        return static::closeBuffers($flush);
    }

    /**
     * Clears a config group from the cached configuration.
     *
     * @param   string  config group
     * @return  void
     * @deprecated
     */
    public static function config_clear($group) {
        // Remove the group from config
        unset(self::$configuration[$group], self::$internal_cache['configuration'][$group]);

        if (!isset(self::$write_cache['configuration'])) {
            // Cache has changed
            self::$write_cache['configuration'] = TRUE;
        }
    }

    /**
     * Retrieves current user agent information:
     * keys:  browser, version, platform, mobile, robot, referrer, languages, charsets
     * tests: is_browser, is_mobile, is_robot, accept_lang, accept_charset
     *
     * @param   string   key or test name
     * @param   string   used with "accept" tests: user_agent(accept_lang, en)
     * @return  array    languages and charsets
     * @return  string   all other keys
     * @return  boolean  all tests
     */
    public static function user_agent($key = 'agent', $compare = NULL) {
        return static::userAgent($key, $compare);
    }

}
