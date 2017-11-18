<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CConfig {

    protected static $instances = array();
    protected $group = 'app';
    protected $configs = array();

    protected function __construct($group) {
        $files = CF::find_file('config', $group);

        //add backward compatibility
        //TODO: remove folder config in DOCROOT
        if (!is_array($files))
            $files = array();
        if (file_exists(DOCROOT . 'config' . DS . $group . EXT)) {
            $files[] = DOCROOT . 'config' . DS . $group . EXT;
        }
        //reverse ordering to set priority
        if ($files == null) {
            //var_dump(debug_backtrace());
            //throw new CF_Exception('file config '.$group.' not found');
            $files = array();
        }
        $this->group = $group;

        foreach ($files as $file) {


            $cfg = include $file;
            if (!is_array($cfg)) {
                //backward compatibility with older config
                if (isset($config)) {
                    $cfg = $config;
                }
            }
            if (!is_array($cfg)) {
                //there is an invalid format
                throw new CException("Invalid config format in :file", array(':file' => $file));
            }
            $this->configs = carr::merge($this->configs, $cfg);
        }
    }

    public static function & instance($group = 'app') {
        if (!is_string($group)) {
            throw new CException("Config group must be a string");
        }
        if (!isset(CConfig::$instances[$group])) {
            // Create a new instance
            CConfig::$instances[$group] = new CConfig($group);
        }

        return CConfig::$instances[$group];
    }

    public function get($path = null, $default = null) {
        if ($path === null) {
            return $this->configs;
        }
        return carr::path($this->configs, $path, $default);
    }

    public function set($path, $value) {
        carr::set_path($this->configs, $path, $value);
        return $this;
    }

}
