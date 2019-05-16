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

    /**
     * 
     * @param string $group
     * @return CConfig
     * @throws CException
     */
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

    /**
     * flatten the config array, retrieves information of default value and file which config is created
     * 
     * @throws CException
     */
    public function getConfigData() {
        $files = CF::findFile('config', $this->group);

        //add backward compatibility
        //TODO: remove folder config in DOCROOT
        if (!is_array($files)) {
            $files = array();
        }
        if (file_exists(DOCROOT . 'config' . DS . $this->group . EXT)) {
            $files[] = DOCROOT . 'config' . DS . $this->group . EXT;
        }
        //reverse ordering to set priority
        if ($files == null) {
            //var_dump(debug_backtrace());
            //throw new CF_Exception('file config '.$group.' not found');
            $files = array();
        }

        $resultFiles = array();
        $resultData = array();
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
            $cfgFiles = $cfg;
            foreach ($cfgFiles as $key => $val) {
                $cfgFiles[$key] = $file;
            }
            $resultFiles = carr::merge($resultFiles, $cfgFiles);
            $resultData = carr::merge($resultData, $cfg);
        }

        //we will flatten the array of result Data
        $result = array();
        $addToResult = function($key, $value, &$result) use($resultFiles) {
            $keyParts = explode('.', $key);
            $resultData = array();
            $resultData['key'] = $key;
            $resultData['value'] = $value;
            $resultData['type'] = gettype($value);
            $file = carr::get($resultFiles, carr::first($keyParts));
            $resultData['file'] = $file;



            $result[] = $resultData;
        };
        $flatten = function($array, $keyPath = '') use (&$flatten, $addToResult, &$result) {
            foreach ($array as $key => $value) {
                $keyDotted = strlen($keyPath) == 0 ? $key : $keyPath . '.' . $key;
                if (is_array($value)) {
                    if (carr::isAssoc($value)) {
                        //need to be flatten
                        $flatten($value, $keyDotted);
                    } else {
                        $addToResult($keyDotted, $value, $result);
                    }
                } else {
                    $addToResult($keyDotted, $value, $result);
                }
            }
        };
        $flatten($resultData);
        return $result;
    }

}
