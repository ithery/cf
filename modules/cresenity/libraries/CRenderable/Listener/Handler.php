<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 3:50:35 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CRenderable_Listener_Handler {

    const TYPE_REMOVE = 'remove';
    const TYPE_RELOAD = 'reload';
    const TYPE_SUBMIT = 'submit';
    const TYPE_DIALOG = 'dialog';
    const TYPE_EMPTY = 'empty';
    const TYPE_CUSTOM = 'custom';
    const TYPE_APPEND = 'append';

    protected $name;
    protected $handlers;
    protected $driver;

    public function __construct($owner, $event, $name) {

        $this->name = ucfirst($name);
        // Set driver name
        $driver = 'CRenderable_Listener_Handler_Driver_' . $this->name;

        try {
            // Validation of the driver
            $class = new ReflectionClass($driver);
            // Initialize the driver
            $this->driver = $class->newInstance($owner, $event, $this->name);
        } catch (ReflectionException $ex) {

            throw new CRenderable_Listener_Handler_Exception('The :driver driver for the :class library could not be found', array(':driver' => ucfirst($this->name), ':class' => get_class($this)));
        }
    }

    public function js() {
        return $this->driver->script();
    }

    /**
     * 
     * @param string $param
     * @return CHandler
     */
    public function set_url_param($param) {
        $this->driver->set_url_param($param);
        return $this;
    }

    /**
     * 
     * @param type $method
     * @param type $args
     * @return \CHandler
     */
    public function __call($method, $args) {
        if (!count($args)) {
            $this->driver->$method($args);
        } else {
            $str = '';

            $values = array_values($args);
            for ($i = 0; $i < count($values); $i++) {
                if (strlen($str) > 0)
                    $str .= ",";
                $str .= "" . cphp::string_value($values[$i]) . "";
            }
            //$str = substr($str, 0, -2);
            eval('$this->driver->' . $method . '(' . $str . ');');
        }

        //$this->driver->$method($args);
        return $this;
    }

    public function content() {
        return $this->driver->content();
    }

}
