<?php

defined('SYSPATH') OR die('No direct access allowed.');

final class CManager {

    private static $_instance;
    protected static $controls = array();
    protected static $controls_code = array();
    protected static $elements = array();
    protected static $elements_code = array();
    protected static $is_mobile = false;
    protected static $mobile_path = '';
    protected static $theme_data = null;

    /**
     *
     * @var CManager_Theme
     */
    protected static $theme = null;
    
    /**
     *
     * @return CManager
     */
    public static function instance() {
        if (self::$_instance == null) {
            self::$_instance = new CManager();
        }
        return self::$_instance;
    }

    public function __construct() {
        $this->is_mobile = ccfg::get('is_mobile');
        $this->mobile_path = '';

//            $theme = ccfg::get('theme');
//            if ($theme == null) $theme = 'cresenity';
        $theme = ctheme::get_current_theme();
        $theme_file = CF::get_file('themes', $theme);
        if (file_exists($theme_file)) {
            $this->theme_data = include $theme_file;
            $bootstrap = carr::get($this->theme_data, 'bootstrap');
            if (strlen($bootstrap) > 0) {
                $this->bootstrap = carr::get($this->theme_data, 'bootstrap');
            }
        }
        self::$theme = new CManager_Theme();
    }

    /**
     * 
     * @return CManager_Theme
     */
    public static function theme() {
        return self::$theme;
    }
    
    public static function get_theme_data() {
        return self::instance()->theme_data;
    }

    public static function set_theme_data($theme_data) {
        self::instance()->theme_data = $theme_data;
        return self::instance();
    }

    /**
     * 
     * @param string $module
     * @param array $data optional
     * @return boolean
     */
    public static function registerModule($module, $data = array()) {
        if (!empty($data)) {
            CClientModules::instance()->defineModule($module, $data);
        }
        return CClientModules::instance()->register_module($module);
    }

    /**
     * backward compatibility of registerModule
     * 
     * @param string $module
     * @param array $data optional
     * @return boolean
     */
    public static function register_module($module, $data = array()) {
        return self::registerModule($module, $data);
    }

    public static function unregister_module($module) {
        return CClientModules::instance()->unregister_module($module);
    }

    public static function registerControl($type, $class, $codePath = '') {
        return self::instance()->register_control($type, $class, $codePath);
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

    /**
     * 
     * @param string $type
     * @param string $class
     * @param string $codePath
     * @return CElement
     */
    public static function registerElement($type, $class, $codePath = '') {
        return self::instance()->register_element($type, $class, $codePath);
    }

    public function register_element($type, $class, $code_path = '') {
        $this->elements[$type] = $class;
        $this->elements_code[$type] = $code_path;
        if (strlen($code_path) > 0) {
            if (file_exists($code_path)) {
                include $code_path;
            } else {
                trigger_error('File ' . $code_path . ' Not Exists');
            }
        }
    }

    /**
     * 
     * @param string $type
     * @return boolean
     */
    public static function isRegisteredControl($type) {
        return self::instance()->is_registered_control($type);
    }

    public function is_registered_control($type) {
        return isset($this->controls[$type]);
    }

    /**
     * 
     * @return array
     */
    public static function getRegisteredControls() {
        return self::instance()->get_registered_controls();
    }

    public function get_registered_controls() {
        return $this->controls;
    }

    /**
     * 
     * @param string $type
     * @return boolean
     */
    public static function isRegisteredElement($type) {
        return self::instance()->is_registered_element($type);
    }

    public function is_registered_element($type) {
        return isset($this->elements[$type]);
    }

    public function create_control($id, $type) {
        if (!isset($this->controls[$type])) {
            trigger_error('Type of control ' . $type . ' not registered');
        }
        $class = $this->controls[$type];

        if (cstr::startsWith($class, 'CElement_FormInput')) {
            return CElement_Factory::createFormInput($class, $id);
        }

        return call_user_func(array($class, 'factory'), ($id));

        //$obj = eval('new '.$class.'::factory("'.$id.'")');
        //return $obj;
        //return $class::factory($id);
    }

    public function create_element($id, $type) {
        if (!isset($this->elements[$type])) {
            trigger_error('Type of element ' . $type . ' not registered');
        }
        $class = $this->elements[$type];

        if (cstr::startsWith($class, 'CElement_Element')) {
            return CElement_Factory::createElement($id);
        }
        return call_user_func(array($class, 'factory'), ($id));

        //$obj = eval('new '.$class.'::factory("'.$id.'")');
        //return $obj;
        //return $class::factory($id);
    }

    public function set_mobile_path($path) {
        $this->mobile_path = $path;
        return $this;
    }

    public function get_mobile_path() {
        return $this->mobile_path;
    }

    public function is_mobile() {
        return $this->is_mobile;
    }

}
