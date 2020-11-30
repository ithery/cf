<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jan 6, 2018, 6:33:59 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTemplate {

    /**
     * The stack of section names currently being captured.
     * @var array
     */
    private $capture;

    /**
     * A collection point for section content.
     * @var array
     */
    private $section;

    /**
     * Default folder for templates view
     * @var string
     */
    protected $templateFolder = 'templates';

    /**
     * Data assigned to the template.
     * @var array
     */
    protected $data = array();
    protected $name;
    protected $registry;
    protected $blockRoutingCallback = null;

    /**
     * An aribtrary object for helpers.
     * @var object
     */
    private $helpers;

    public function __construct($name, $data = array()) {
        $this->registry = new CTemplate_Registry();

        $filename = CF::findFile($this->templateFolder, $name, TRUE);
        $this->name = $name;
        $this->registry->set($name, $filename);
        if ($data === NULL || !is_array($data)) {
            $data = array();
        }
        $this->helpers = new CTemplate_Helpers();
        $this->data = $data;
    }

    public function setBlockRoutingCallback(callable $callback) {
        $this->blockRoutingCallback = $callback;
    }

    public static function factory($name, $data = array()) {
        return new CTemplate($name, $data);
    }

    public function block($name, $data = array()) {
        if ($this->blockRoutingCallback != null && is_callable($this->blockRoutingCallback)) {
            $name = call_user_func_array($this->blockRoutingCallback, array($name));
        }
        $filename = CF::findFile($this->templateFolder, $name, TRUE);
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
    public function __get($key) {
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
        try {
            $this->getRegistry($this->name)->__invoke($this->data);
        } catch (Exception $ex) {
            ob_end_clean();
            throw $ex;
        }
        return ob_get_clean();
    }

    /**
     *
     * Is a particular named section available?
     *
     * @param string $name The section name.
     * @return bool
     */
    protected function hasSection($name) {
        return isset($this->section[$name]);
    }

    /**
     *
     * Sets the body of a named section directly, as opposed to buffering and
     * capturing output.
     *
     * @param string $name The section name.
     * @param string $body The section body.
     * @return null
     */
    protected function setSection($name, $body) {
        $this->section[$name] = $body;
    }

    /**
     *
     * Gets the body of a named section.
     *
     * @param string $name The section name.
     * @return string
     */
    protected function getSection($name) {
        return $this->section[$name];
    }

    /**
     *
     * Begins output buffering for a named section.
     *
     * @param string $name The section name.
     * @return null
     *
     */
    protected function beginSection($name) {
        $this->capture[] = $name;
        ob_start();
    }

    /**
     *
     * Ends buffering and retains output for the most-recent section.
     * @return null
     */
    protected function endSection() {
        $body = ob_get_clean();
        $name = array_pop($this->capture);
        $this->setSection($name, $body);
    }

}
