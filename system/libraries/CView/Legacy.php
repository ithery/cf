<?php

/**
 * Description of Legacy
 *
 * @author Hery
 */
defined('SYSPATH') or die('No direct access allowed.');

class CView_Legacy {
    protected static $viewFolder = 'views';
    // The view file name and type
    protected $filename = false;
    protected $filetype = false;
    // CView variable storage
    protected $local_data = [];
    protected static $global_data = [];

    /**
     * Creates a new CView using the given parameters.
     *
     * @param   string  view name
     * @param   array   pre-load data
     * @param   string  type of file: html, css, js, etc.
     * @param null|mixed $name
     * @param null|mixed $data
     * @param null|mixed $type
     *
     * @return object
     */
    public static function factory($name = null, $data = null, $type = null) {
        return new CView_Legacy($name, $data, $type);
    }

    /**
     * Check a CView is exists.
     *
     * @param   string  view name
     * @param mixed $name
     *
     * @return boolean
     */
    public static function exists($name) {
        $filename = CF::find_file(self::$viewFolder, $name, false);
        return strlen($filename) > 0;
    }

    /**
     * Attempts to load a view and pre-load view data.
     *
     * @throws CF_Exception if the requested view cannot be found
     *
     * @param   string  view name
     * @param   array   pre-load data
     * @param   string  type of file: html, css, js, etc.
     * @param null|mixed $name
     * @param null|mixed $data
     * @param null|mixed $type
     *
     * @return void
     */
    public function __construct($name = null, $data = null, $type = null) {
        if (is_string($name) and $name !== '') {
            // Set the filename
            $this->set_filename($name, $type);
        }

        if (is_array($data) and !empty($data)) {
            // Preload data using array_merge, to allow user extensions
            $this->local_data = array_merge($this->local_data, $data);
        }
    }

    /**
     * Magic method access to test for view property
     *
     * @param   string   CView property to test for
     * @param null|mixed $key
     *
     * @return boolean
     */
    public function __isset($key = null) {
        return $this->is_set($key);
    }

    /**
     * Sets the view filename.
     *
     * @chainable
     *
     * @param   string  view filename
     * @param   string  view file type
     * @param mixed      $name
     * @param null|mixed $type
     *
     * @return object
     */
    public function set_filename($name, $type = null) {
        if ($type == null) {
            // Load the filename and set the content type
            $this->filename = CF::find_file(self::$viewFolder, $name, true);
            $this->filetype = EXT;
        } else {
            // Check if the filetype is allowed by the configuration
            if (!in_array($type, CF::config('view.allowed_filetypes'))) {
                throw new CF_Exception('core.invalid_filetype', $type);
            }
            // Load the filename and set the content type
            $this->filename = CF::find_file(self::$viewFolder, $name, true, $type);
            $this->filetype = CF::config('mimes.' . $type);

            if ($this->filetype == null) {
                // Use the specified type
                $this->filetype = $type;
            }
        }

        return $this;
    }

    /**
     * Sets a view variable.
     *
     * @param   string|array  name of variable or an array of variables
     * @param   mixed         value when using a named variable
     * @param mixed      $name
     * @param null|mixed $value
     *
     * @return object
     */
    public function set($name, $value = null) {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->__set($key, $value);
            }
        } else {
            $this->__set($name, $value);
        }

        return $this;
    }

    /**
     * Checks for a property existence in the view locally or globally. Unlike the built in __isset(),
     * this method can take an array of properties to test simultaneously.
     *
     * @param string $key property name to test for
     * @param array  $key array of property names to test for
     *
     * @return boolean property test result
     * @return array   associative array of keys and boolean test result
     */
    public function is_set($key = false) {
        // Setup result;
        $result = false;

        // If key is an array
        if (is_array($key)) {
            // Set the result to an array
            $result = [];

            // Foreach key
            foreach ($key as $property) {
                // Set the result to an associative array
                $result[$property] = (array_key_exists($property, $this->local_data) or array_key_exists($property, self::$global_data)) ? true : false;
            }
        } else {
            // Otherwise just check one property
            $result = (array_key_exists($key, $this->local_data) or array_key_exists($key, self::$global_data)) ? true : false;
        }

        // Return the result
        return $result;
    }

    /**
     * Sets a bound variable by reference.
     *
     * @param   string   name of variable
     * @param   mixed    variable to assign by reference
     * @param mixed $name
     * @param mixed $var
     *
     * @return object
     */
    public function bind($name, &$var) {
        $this->local_data[$name] = &$var;

        return $this;
    }

    /**
     * Sets a view global variable.
     *
     * @param   string|array  name of variable or an array of variables
     * @param   mixed         value when using a named variable
     * @param mixed      $name
     * @param null|mixed $value
     *
     * @return void
     */
    public static function set_global($name, $value = null) {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                self::$global_data[$key] = $value;
            }
        } else {
            self::$global_data[$name] = $value;
        }
    }

    /**
     * Magically sets a view variable.
     *
     * @param   string   variable key
     * @param   string   variable value
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function __set($key, $value) {
        $this->local_data[$key] = $value;
    }

    /**
     * Magically gets a view variable.
     *
     * @param  string  variable key
     * @param mixed $key
     *
     * @return mixed variable value if the key is found
     * @return void  if the key is not found
     */
    public function &__get($key) {
        if (isset($this->local_data[$key])) {
            return $this->local_data[$key];
        }

        if (isset(self::$global_data[$key])) {
            return self::$global_data[$key];
        }

        if (isset($this->$key)) {
            return $this->$key;
        }
    }

    /**
     * Magically converts view object to string.
     *
     * @return string
     */
    public function __toString() {
        try {
            return $this->render();
        } catch (Exception $e) {
            // Display the exception using its internal __toString method
            return (string) $e;
        }
    }

    /**
     * Load a view.
     *
     * @param   view_filename   filename of view
     * @param   input_data  data to pass to view
     * @param mixed $view_filename
     * @param mixed $input_data
     *
     * @return string
     */
    public static function load_view($view_filename, $input_data) {
        if ($view_filename == '') {
            return;
        }

        // Buffering on
        ob_start();

        // Import the view variables to local namespace
        extract($input_data, EXTR_SKIP);

        // Views are straight HTML pages with embedded PHP, so importing them
        // this way insures that $this can be accessed as if the user was in
        // the controller, which gives the easiest access to libraries in views
        try {
            include $view_filename;
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }

        // Fetch the output and close the buffer
        return ob_get_clean();
    }

    /**
     * Renders a view.
     *
     * @param mixed $print
     * @param mixed $renderer
     *
     * @return string if print is FALSE
     */
    public function render($print = false, $renderer = false) {
        if (empty($this->filename)) {
            throw new Exception(CF::lang('core.view_set_filename'));
        }
        if (is_string($this->filetype)) {
            // Merge global and local data, local overrides global with the same name
            $data = array_merge(self::$global_data, $this->local_data);

//            var_dump(CF::$instance);
            // Load the view in the controller for access to $this
            $output = self::load_view($this->filename, $data);

            if ($renderer !== false and is_callable($renderer, true)) {
                // Pass the output through the user defined renderer
                $output = call_user_func($renderer, $output);
            }

            if ($print === true) {
                // Display the output
                echo $output;
                return;
            }
        } else {
            // Set the content type and size
            header('Content-Type: ' . $this->filetype[0]);

            if ($print === true) {
                if ($file = fopen($this->filename, 'rb')) {
                    // Display the output
                    fpassthru($file);
                    fclose($file);
                }
                return;
            }

            // Fetch the file contents
            $output = file_get_contents($this->filename);
        }

        return $output;
    }
}

// End CView_Legacy
