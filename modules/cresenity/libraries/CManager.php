<?php

defined('SYSPATH') OR die('No direct access allowed.');

final class CManager {

    private static $_instance;
    protected $controls = array();
    protected $controls_code = array();

    public static function instance() {
        if (self::$_instance == null) {
            self::$_instance = new CManager();
        }
        return self::$_instance;
    }

    public function register_module($module) {
        return CClientModules::instance()->register_module($module);
    }

    public function register_control($type, $class, $code_path = '') {
        $this->controls[$type] = $class;
        $this->controls_code[$type] = $code_path;
        if (strlen($code_path) > 0) {
            if (file_exists($code_path)) {
                include $code_path;
            } else {
                trigger_error('File ' . $code_path . ' Not Exists');
            }
        }
    }

    public function is_registered_control($type) {
        return isset($this->controls[$type]);
    }

    public function create_control($id, $type) {
        if (!isset($this->controls[$type])) {
            trigger_error('Type of control ' . $type . ' not registered');
        }
        $class = $this->controls[$type];
        return call_user_func(array($class, 'factory'), ($id));

        //$obj = eval('new '.$class.'::factory("'.$id.'")');
        //return $obj;
        //return $class::factory($id);
    }

}
