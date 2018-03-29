<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CView {

    protected static $viewFolder = 'views';
    // The view file name and type
    protected $filename = FALSE;
    protected $filetype = FALSE;
    // CView variable storage
    protected $local_data = array();
    protected static $global_data = array();

    /**
     * Creates a new CView using the given parameters.
     *
     * @param   string  view name
     * @param   array   pre-load data
     * @param   string  type of file: html, css, js, etc.
     * @return  object
     */
    public static function factory($name = NULL, $data = NULL, $type = NULL) {
        return new CView($name, $data, $type);
    }

    /**
     * Check a CView is exists.
     *
     * @param   string  view name
     * @return  boolean
     */
    public static function exists($name) {
        $filename = CF::find_file(self::$viewFolder, $name, false);
        return strlen($filename) > 0;
    }

    /**
     * Attempts to load a view and pre-load view data.
     *
     * @throws  CF_Exception  if the requested view cannot be found
     * @param   string  view name
     * @param   array   pre-load data
     * @param   string  type of file: html, css, js, etc.
     * @return  void
     */
    public function __construct($name = NULL, $data = NULL, $type = NULL) {
        if (is_string($name) AND $name !== '') {
            // Set the filename
            $this->set_filename($name, $type);
        }


        if (is_array($data) AND ! empty($data)) {
            // Preload data using array_merge, to allow user extensions
            $this->local_data = array_merge($this->local_data, $data);
        }
    }

    /**
     * Magic method access to test for view property
     *
     * @param   string   CView property to test for
     * @return  boolean
     */
    public function __isset($key = NULL) {
        return $this->is_set($key);
    }

    /**
     * Sets the view filename.
     *
     * @chainable
     * @param   string  view filename
     * @param   string  view file type
     * @return  object
     */
    public function set_filename($name, $type = NULL) {

        if ($type == NULL) {
            // Load the filename and set the content type
            $this->filename = CF::find_file(self::$viewFolder, $name, TRUE);
            $this->filetype = EXT;
        } else {
            // Check if the filetype is allowed by the configuration
            if (!in_array($type, CF::config('view.allowed_filetypes')))
                throw new CF_Exception('core.invalid_filetype', $type);

            // Load the filename and set the content type
            $this->filename = CF::find_file(self::$viewFolder, $name, TRUE, $type);
            $this->filetype = CF::config('mimes.' . $type);

            if ($this->filetype == NULL) {
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
     * @return  object
     */
    public function set($name, $value = NULL) {
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
     * @param array $key array of property names to test for
     * @return boolean property test result
     * @return array associative array of keys and boolean test result
     */
    public function is_set($key = FALSE) {
        // Setup result;
        $result = FALSE;

        // If key is an array
        if (is_array($key)) {
            // Set the result to an array
            $result = array();

            // Foreach key
            foreach ($key as $property) {
                // Set the result to an associative array
                $result[$property] = (array_key_exists($property, $this->local_data) OR array_key_exists($property, self::$global_data)) ? TRUE : FALSE;
            }
        } else {
            // Otherwise just check one property
            $result = (array_key_exists($key, $this->local_data) OR array_key_exists($key, self::$global_data)) ? TRUE : FALSE;
        }

        // Return the result
        return $result;
    }

    /**
     * Sets a bound variable by reference.
     *
     * @param   string   name of variable
     * @param   mixed    variable to assign by reference
     * @return  object
     */
    public function bind($name, & $var) {
        $this->local_data[$name] = & $var;

        return $this;
    }

    /**
     * Sets a view global variable.
     *
     * @param   string|array  name of variable or an array of variables
     * @param   mixed         value when using a named variable
     * @return  void
     */
    public static function set_global($name, $value = NULL) {
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
     * @return  void
     */
    public function __set($key, $value) {
        $this->local_data[$key] = $value;
    }

    /**
     * Magically gets a view variable.
     *
     * @param  string  variable key
     * @return mixed   variable value if the key is found
     * @return void    if the key is not found
     */
    public function &__get($key) {
        if (isset($this->local_data[$key]))
            return $this->local_data[$key];

        if (isset(self::$global_data[$key]))
            return self::$global_data[$key];

        if (isset($this->$key))
            return $this->$key;
    }

    /**
     * Magically converts view object to string.
     *
     * @return  string
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
     * @return  string    
     */
    public static function load_view($view_filename, $input_data) {
        if ($view_filename == '')
            return;

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
     * @param   boolean   set to TRUE to echo the output instead of returning it
     * @param   callback  special renderer to pass the output through
     * @return  string    if print is FALSE
     * @return  void      if print is TRUE
     */
    public function render($print = FALSE, $renderer = FALSE) {
        if (empty($this->filename)) {
            throw new CF_Exception('core.view_set_filename');
        }
        if (is_string($this->filetype)) {
            // Merge global and local data, local overrides global with the same name
            $data = array_merge(self::$global_data, $this->local_data);

//            var_dump(CF::$instance);
            // Load the view in the controller for access to $this
            $output = self::load_view($this->filename, $data);

            if ($renderer !== FALSE AND is_callable($renderer, TRUE)) {
                // Pass the output through the user defined renderer
                $output = call_user_func($renderer, $output);
            }

            if ($print === TRUE) {
                // Display the output
                echo $output;
                return;
            }
        } else {
            // Set the content type and size
            header('Content-Type: ' . $this->filetype[0]);

            if ($print === TRUE) {
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

// End CView