<?php

    defined('SYSPATH') OR die('No direct access allowed.');

    final class CManager {

        private static $_instance;
        protected $controls = array();
        protected $controls_code = array();
        protected $elements = array();
        protected $elements_code = array();
        protected $is_mobile = false;
        protected $mobile_path = '';
        protected $theme_data = null;

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
            
            $theme = ccfg::get('theme');
            if ($theme == null) $theme = 'cresenity';
            $theme_file = CF::get_file('themes', $theme);
            if (file_exists($theme_file)) {
                $this->theme_data = include $theme_file;
                $bootstrap = carr::get($this->theme_data, 'bootstrap');
                if (strlen($bootstrap) > 0) {
                    $this->bootstrap = carr::get($this->theme_data, 'bootstrap');
                }
            }
        }
        
        function get_theme_data() {
            return $this->theme_data;
        }

        function set_theme_data($theme_data) {
            $this->theme_data = $theme_data;
            return $this;
        }

        
        public function register_module($module) {
            return CClientModules::instance()->register_module($module);
        }

        public function unregister_module($module) {
            return CClientModules::instance()->unregister_module($module);
        }

        public function register_control($type, $class, $code_path = '') {
            $this->controls[$type] = $class;
            $this->controls_code[$type] = $code_path;
            if (strlen($code_path) > 0) {
                if (file_exists($code_path)) {
                    include $code_path;
                }
                else {
                    trigger_error('File ' . $code_path . ' Not Exists');
                }
            }
        }

        public function register_element($type, $class, $code_path = '') {
            $this->elements[$type] = $class;
            $this->elements_code[$type] = $code_path;
            if (strlen($code_path) > 0) {
                if (file_exists($code_path)) {
                    include $code_path;
                }
                else {
                    trigger_error('File ' . $code_path . ' Not Exists');
                }
            }
        }

        public function is_registered_control($type) {
            return isset($this->controls[$type]);
        }

        public function is_registered_element($type) {
            return isset($this->elements[$type]);
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

        public function create_element($id, $type) {
            if (!isset($this->elements[$type])) {
                trigger_error('Type of element ' . $type . ' not registered');
            }
            $class = $this->elements[$type];
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
    