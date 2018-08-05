<?php

class CHandler extends CObject {

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

    protected function __construct($owner, $event, $name) {
        parent::__construct();


        $this->name = $name;

        $driver = 'CHandler_' . ucfirst($this->name) . '_Driver';
        $driver_file = dirname(__FILE__) . "/drivers/CHandler/" . ucfirst($this->name) . EXT;
        if (!class_exists('CHandler_Driver')) {
            require_once dirname(__FILE__) . "/drivers/CHandler" . EXT;
        }
        if (!file_exists($driver_file)) {
            throw new CHandler_Exception('core.driver_not_found', $this->name, get_class($this));
        } else {
            if (!class_exists($driver)) {
                require_once $driver_file;
            }
        }

        $this->driver = new $driver($owner, $event, $this->name);
    }

    public static function factory($owner, $event, $name) {
        return new CHandler($owner, $event, $name);
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

// End Kohana Database Exception
