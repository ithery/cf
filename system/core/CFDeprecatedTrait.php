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
     * @deprecated
     */
    public static function user_agent($key = 'agent', $compare = NULL) {
        return static::userAgent($key, $compare);
    }

    /**
     * Returns the value of a key, defined by a 'dot-noted' string, from an array.
     *
     * @param   array   array to search
     * @param   string  dot-noted string: foo.bar.baz
     * @return  string  if the key is found
     * @return  void    if the key is not found
     * @deprecated
     */
    public static function key_string($array, $keys) {
        if (empty($array))
            return NULL;

        // Prepare for loop
        $keys = explode('.', $keys);

        do {
            // Get the next key
            $key = array_shift($keys);

            if (isset($array[$key])) {
                if (is_array($array[$key]) AND!empty($keys)) {
                    // Dig down to prepare the next loop
                    $array = $array[$key];
                } else {
                    // Requested key was found
                    return $array[$key];
                }
            } else {
                // Requested key is not set
                break;
            }
        } while (!empty($keys));

        return NULL;
    }

    /**
     * Sets values in an array by using a 'dot-noted' string.
     *
     * @param   array   array to set keys in (reference)
     * @param   string  dot-noted string: foo.bar.baz
     * @return  mixed   fill value for the key
     * @return  void
     * @deprecated
     */
    public static function key_string_set(& $array, $keys, $fill = NULL) {
        if (is_object($array) AND ( $array instanceof ArrayObject)) {
            // Copy the array
            $array_copy = $array->getArrayCopy();

            // Is an object
            $array_object = TRUE;
        } else {
            if (!is_array($array)) {
                // Must always be an array
                $array = (array) $array;
            }

            // Copy is a reference to the array
            $array_copy = & $array;
        }

        if (empty($keys))
            return $array;

        // Create keys
        $keys = explode('.', $keys);

        // Create reference to the array
        $row = & $array_copy;

        for ($i = 0, $end = count($keys) - 1; $i <= $end; $i++) {
            // Get the current key
            $key = $keys[$i];

            if (!isset($row[$key])) {
                if (isset($keys[$i + 1])) {
                    // Make the value an array
                    $row[$key] = array();
                } else {
                    // Add the fill key
                    $row[$key] = $fill;
                }
            } elseif (isset($keys[$i + 1])) {
                // Make the value an array
                $row[$key] = (array) $row[$key];
            }

            // Go down a level, creating a new row reference
            $row = & $row[$key];
        }

        if (isset($array_object)) {
            // Swap the array back in
            $array->exchangeArray($array_copy);
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
     * @deprecated
     */
    public static function userAgent($key = 'agent', $compare = NULL) {
        static $info;

        // Return the raw string
        if ($key === 'agent')
            return self::$user_agent;

        if ($info === NULL) {
            // Parse the user agent and extract basic information
            $agents = self::config('user_agents');

            foreach ($agents as $type => $data) {
                foreach ($data as $agent => $name) {
                    if (stripos(self::$user_agent, $agent) !== FALSE) {
                        if ($type === 'browser' AND preg_match('|' . preg_quote($agent) . '[^0-9.]*+([0-9.][0-9.a-z]*)|i', self::$user_agent, $match)) {
                            // Set the browser version
                            $info['version'] = $match[1];
                        }

                        // Set the agent name
                        $info[$type] = $name;
                        break;
                    }
                }
            }
        }

        if (empty($info[$key])) {
            switch ($key) {
                case 'is_robot':
                case 'is_browser':
                case 'is_mobile':
                    // A boolean result
                    $return = !empty($info[substr($key, 3)]);
                    break;
                case 'languages':
                    $return = array();
                    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                        if (preg_match_all('/[-a-z]{2,}/', strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE'])), $matches)) {
                            // Found a result
                            $return = $matches[0];
                        }
                    }
                    break;
                case 'charsets':
                    $return = array();
                    if (!empty($_SERVER['HTTP_ACCEPT_CHARSET'])) {
                        if (preg_match_all('/[-a-z0-9]{2,}/', strtolower(trim($_SERVER['HTTP_ACCEPT_CHARSET'])), $matches)) {
                            // Found a result
                            $return = $matches[0];
                        }
                    }
                    break;
                case 'referrer':
                    if (!empty($_SERVER['HTTP_REFERER'])) {
                        // Found a result
                        $return = trim($_SERVER['HTTP_REFERER']);
                    }
                    break;
            }

            // Cache the return value
            isset($return) and $info[$key] = $return;
        }

        if (!empty($compare)) {
            // The comparison must always be lowercase
            $compare = strtolower($compare);

            switch ($key) {
                case 'accept_lang':
                    // Check if the lange is accepted
                    return in_array($compare, self::user_agent('languages'));
                    break;
                case 'accept_charset':
                    // Check if the charset is accepted
                    return in_array($compare, self::user_agent('charsets'));
                    break;
                default:
                    // Invalid comparison
                    return FALSE;
                    break;
            }
        }

        // Return the key, if set
        return isset($info[$key]) ? $info[$key] : NULL;
    }

    /**
     * Load a config file.
     *
     * @param   string   config filename, without extension
     * @param   boolean  is the file required?
     * @return  array
     * @deprecated
     */
    public static function config_load($name, $required = TRUE) {
        if ($name === 'core') {
            $found = FALSE;

            // find config file at all available paths
            if ($files = self::findFile('config', 'config', $required)) {
                foreach ($files as $file) {
                    if (file_exists($file)) {
                        require $file;
                        $found = TRUE;
                    }
                }
            }

            if ($found == FALSE) {
                // Load the application configuration file
                if (file_exists(DOCROOT . 'config/config' . EXT)) {
                    require DOCROOT . 'config/config' . EXT;
                    $found = TRUE;
                }
            }

            if (!isset($config['site_domain'])) {
                // Invalid config file
                die('Your CF application configuration file is not valid.');
            }

            return $config;
        }

        if (isset(self::$internal_cache['configuration'][$name]))
            return self::$internal_cache['configuration'][$name];

        // Load matching configs
        $configuration = array();

        if ($files = self::findFile('config', $name, $required)) {
            foreach ($files as $file) {
                require $file;

                if (isset($config) AND is_array($config)) {
                    // Merge in configuration
                    $configuration = array_merge($configuration, $config);
                }
            }
        }

        if (!isset(self::$write_cache['configuration'])) {
            // Cache has changed
            self::$write_cache['configuration'] = TRUE;
        }

        return self::$internal_cache['configuration'][$name] = $configuration;
    }

    /**
     * Sets a configuration item, if allowed.
     *
     * @param   string   config key string
     * @param   string   config value
     * @return  boolean
     * @deprecated
     */
    public static function config_set($key, $value) {
        // Do this to make sure that the config array is already loaded
        self::config($key);

        if (substr($key, 0, 7) === 'routes.') {
            // Routes cannot contain sub keys due to possible dots in regex
            $keys = explode('.', $key, 2);
        } else {
            // Convert dot-noted key string to an array
            $keys = explode('.', $key);
        }

        // Used for recursion
        $conf = & self::$configuration;
        $last = count($keys) - 1;

        foreach ($keys as $i => $k) {
            if ($i === $last) {
                $conf[$k] = $value;
            } else {
                $conf = & $conf[$k];
            }
        }

        if ($key === 'core.modules') {
            // Reprocess the include paths
            self::include_paths(TRUE);
        }

        return TRUE;
    }

    /**
     * Displays nice backtrace information.
     * @see http://php.net/debug_backtrace
     *
     * @param   array   backtrace generated by an exception or debug_backtrace
     * @return  string
     * @deprecated
     */
    public static function backtrace($trace) {
        if (!is_array($trace))
            return;

        // Final output
        $output = array();

        foreach ($trace as $entry) {
            $temp = '<li>';

            if (isset($entry['file'])) {
                $temp .= self::lang('core.error_file_line', ['file' => preg_replace('!^' . preg_quote(DOCROOT) . '!', '', $entry['file']), 'line' => $entry['line']]);
            }

            $temp .= '<pre>';

            if (isset($entry['class'])) {
                // Add class and call type
                $temp .= $entry['class'] . $entry['type'];
            }

            // Add function
            $temp .= $entry['function'] . '( ';

            // Add function args
            if (isset($entry['args']) AND is_array($entry['args'])) {
                // Separator starts as nothing
                $sep = '';

                while ($arg = array_shift($entry['args'])) {
                    if (is_string($arg) AND self::isFile($arg)) {
                        // Remove docroot from filename
                        $arg = preg_replace('!^' . preg_quote(DOCROOT) . '!', '', $arg);
                    }

                    $temp .= $sep . chtml::specialchars(@print_r($arg, TRUE));

                    // Change separator to a comma
                    $sep = ', ';
                }
            }

            $temp .= ' )</pre></li>';

            $output[] = $temp;
        }

        return '<ul class="backtrace">' . implode("\n", $output) . '</ul>';
    }

    /**
     * 
     * @param type $domain
     * @return type
     * @deprecated
     */
    public static function domain_data($domain) {
        $data = CFData::get($domain, 'domain');
        $result = array();
        $result['app_id'] = '';
        $result['app_code'] = '';
        $result['org_id'] = '';
        $result['org_code'] = '';
        $result['store_id'] = '';
        $result['store_code'] = '';
        $result['shared_app_code'] = array();
        $result['theme'] = '';

        if ($data != null) {
            $result['app_id'] = isset($data['app_id']) ? $data['app_id'] : null;
            $result['app_code'] = isset($data['app_code']) ? $data['app_code'] : null;
            $result['org_id'] = isset($data['org_id']) ? $data['org_id'] : null;
            $result['org_code'] = isset($data['org_code']) ? $data['org_code'] : null;
            $result['store_id'] = isset($data['store_id']) ? $data['store_id'] : null;
            $result['store_code'] = isset($data['store_code']) ? $data['store_code'] : null;
            $result['shared_app_code'] = isset($data['shared_app_code']) ? $data['shared_app_code'] : array();
            $result['theme'] = isset($data['theme']) ? $data['theme'] : null;
        }
        return $result;
    }

}
