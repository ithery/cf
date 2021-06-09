<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jan 6, 2018, 6:33:59 PM
 */
class CTemplate {
    /**
     * The stack of section names currently being captured.
     *
     * @var array
     */
    private $capture;

    /**
     * A collection point for section content.
     *
     * @var array
     */
    private $section;

    /**
     * Default folder for templates view
     *
     * @var string
     */
    protected $templateFolder = 'templates';

    /**
     * Data assigned to the template.
     *
     * @var array
     */
    protected $data = [];
    protected $name;
    protected $registry;
    protected $blockRoutingCallback = null;

    /**
     * An aribtrary object for helpers.
     *
     * @var object
     */
    private $helpers;

    public function __construct($name, $data = []) {
        $this->registry = new CTemplate_Registry();

        $filename = CF::findFile($this->templateFolder, $name, true);
        $this->name = $name;
        $this->registry->set($name, $filename);
        if ($data === null || !is_array($data)) {
            $data = [];
        }
        $this->helpers = new CTemplate_Helpers();
        $this->data = $data;
    }

    public function setBlockRoutingCallback(callable $callback) {
        $this->blockRoutingCallback = $callback;
    }

    public static function factory($name, $data = []) {
        return new CTemplate($name, $data);
    }

    public function block($name, $data = []) {
        if ($this->blockRoutingCallback != null && is_callable($this->blockRoutingCallback)) {
            $name = call_user_func_array($this->blockRoutingCallback, [$name]);
        }
        $filename = CF::findFile($this->templateFolder, $name, true);
        $this->registry->set($name, $filename);
        if ($data === null || !is_array($data)) {
            $data = [];
        }
        $data = array_merge($this->data, $data);
        ob_start();
        $this->getRegistry($name)->__invoke($data);
        return ob_get_clean();
    }

    /**
     * Magically sets a view variable.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function __set($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * Magically gets a view variable.
     *
     * @param mixed $key
     *
     * @return void|mixed variable value if the key is found
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
     *
     * @param string $name The helper object method name.
     * @param array  $args The arguments to pass to the helper.
     *
     * @return mixed
     */
    public function __call($name, $args) {
        return call_user_func_array([$this->helpers, $name], $args);
    }

    /**
     * Sets a view variable.
     *
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

    protected function getRegistry($name) {
        $tmpl = $this->registry->get($name);
        return $tmpl->bindTo($this, get_class($this));
    }

    /**
     * Gets the arbitrary object for helpers.
     *
     * @return object
     */
    public function getHelpers() {
        return $this->helpers;
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
     * Is a particular named section available?
     *
     * @param string $name The section name.
     *
     * @return bool
     */
    protected function hasSection($name) {
        return isset($this->section[$name]);
    }

    /**
     * Sets the body of a named section directly, as opposed to buffering and
     * capturing output.
     *
     * @param string $name The section name.
     * @param string $body The section body.
     *
     * @return null
     */
    protected function setSection($name, $body) {
        $this->section[$name] = $body;
    }

    /**
     * Gets the body of a named section.
     *
     * @param string $name The section name.
     *
     * @return string
     */
    protected function getSection($name) {
        return $this->section[$name];
    }

    /**
     * Begins output buffering for a named section.
     *
     * @param string $name The section name.
     *
     * @return null
     */
    protected function beginSection($name) {
        $this->capture[] = $name;
        ob_start();
    }

    /**
     * Ends buffering and retains output for the most-recent section.
     *
     * @return null
     */
    protected function endSection() {
        $body = ob_get_clean();
        $name = array_pop($this->capture);
        $this->setSection($name, $body);
    }
}
