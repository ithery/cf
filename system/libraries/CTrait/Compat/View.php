<?php

/**
 * Description of View
 *
 * @author Hery
 */
trait CTrait_Compat_View {

    protected static $global_data = array();

    /**
     * Load a view.
     *
     * @param   view_filename   filename of view
     * @param   input_data  data to pass to view
     * @return  string    
     * @deprecated
     */
    public static function load_view($view_filename, $input_data) {
        return static::loadView($view_filename, $input_data);
    }

    /**
     * Sets the view filename.
     *
     * @chainable
     * @param   string  view filename
     * @param   string  view file type
     * @return  object
     * @deprecated
     */
    public function set_filename($name, $type = NULL) {
        return $this->setFilename($name, $type);
    }

    /**
     * Sets a view global variable.
     *
     * @param   string|array  name of variable or an array of variables
     * @param   mixed         value when using a named variable
     * @return  void
     * @deprecated
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

}
