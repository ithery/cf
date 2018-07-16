<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jan 6, 2018, 6:33:59 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTemplate {

    protected $templateFolder = 'templates';

    /**
     * Data assigned to the template.
     * @var array
     */
    protected $data = array();
    protected $name;
    protected $registry;

    /**
     * An aribtrary object for helpers.
     * @var object
     */
    private $helpers;

    public function __construct($name, $data = array()) {
        $this->registry = new CTemplate_Registry();
        $filename = CF::find_file($this->templateFolder, $name, TRUE);
        $this->name = $name;
        $this->registry->set($name, $filename);
        if ($data === NULL || !is_array($data)) {
            $data = array();
        }
        $this->helpers = new CTemplate_Helpers();
        $this->data = $data;
    }

    public static function factory($name, $data = array()) {
        return new CTemplate($name,$data);
    }
    
    public function block($name, $data = array()) {
        $filename = CF::find_file($this->templateFolder, $name, TRUE);
        $this->registry->set($name, $filename);
        if ($data === NULL || !is_array($data)) {
            $data = array();
        }
        $data = array_merge($this->data, $data);
        ob_start();
        $this->getRegistry($name)->__invoke($data);
        return ob_get_clean();
    }

    /**
     * Magically sets a view variable.
     *
     * @param   string   variable key
     * @param   string   variable value
     * @return  void
     */
    public function __set($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * Magically gets a view variable.
     *
     * @param  string  variable key
     * @return mixed   variable value if the key is found
     * @return void    if the key is not found
     */
    public function &__get($key) {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        if (isset($this->$key)) {
            return $this->$key;
        }
    }

    /**
     * Magic call to expose helper object methods as template methods.
     * @param string $name The helper object method name.
     * @param array $args The arguments to pass to the helper.
     * @return mixed
     */
    public function __call($name, $args) {
        return call_user_func_array(array($this->helpers, $name), $args);
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

    protected function getRegistry($name) {
        $tmpl = $this->registry->get($name);
        return $tmpl->bindTo($this, get_class($this));
    }

    /**
     *
     * Gets the arbitrary object for helpers.
     *
     * @return object
     *
     */
    public function getHelpers() {
        return $this->helpers;
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
        ob_start();
        $this->getRegistry($this->name)->__invoke($this->data);
        return ob_get_clean();
    }

}
