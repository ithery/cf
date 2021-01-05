<?php

defined('SYSPATH') or die('No direct access allowed.');

class CConfig implements CInterface_Arrayable, ArrayAccess {
    protected static $instances = [];
    protected $group;
    protected $items;

    /**
     * @param string $group
     *
     * @return CConfig
     *
     * @throws CException
     */
    public static function &instance($group = 'app') {
        if (!is_string($group)) {
            throw new CException('Config group must be a string');
        }
        if (!isset(CConfig::$instances[$group])) {
            // Create a new instance
            CConfig::$instances[$group] = new CConfig($group);
        }

        return CConfig::$instances[$group];
    }

    protected function __construct($group) {
        $this->group = $group;
        $this->refresh();
    }

    public function get($key = null, $default = null) {
        if ($key == null) {
            return $this->all();
        }
        if (is_array($key)) {
            return $this->getMany($key);
        }

        return carr::get($this->items, $key, $default);
    }

    /**
     * Get many configuration values.
     *
     * @param array $keys
     *
     * @return array
     */
    public function getMany($keys) {
        $config = [];

        foreach ($keys as $key => $default) {
            if (is_numeric($key)) {
                list($key, $default) = [$default, null];
            }

            $config[$key] = carr::get($this->items, $key, $default);
        }

        return $config;
    }

    public function set($key, $value) {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            carr::set($this->items, $key, $value);
        }
        return $this;
    }

    public function refresh() {
        $this->items = [];
        $files = CF::findFile('config', $this->group, $required = false, $ext = false, $refresh = true);

        //add backward compatibility
        //TODO: remove folder config in DOCROOT
        if (!is_array($files)) {
            $files = [];
        }
        if (file_exists(DOCROOT . 'config' . DS . $this->group . EXT)) {
            $files[] = DOCROOT . 'config' . DS . $this->group . EXT;
        }

        foreach ($files as $file) {
            $cfg = include $file;
            if (!is_array($cfg)) {
                //backward compatibility with older config
                if (isset($config)) {
                    $cfg = $config;
                    unset($config);
                }
            }
            if (!is_array($cfg)) {
                //there is an invalid format
                throw new CException('Invalid config format in :file', [':file' => $file]);
            }
            $this->items = carr::merge($this->items, $cfg);
        }
    }

    /**
     * Flatten the config array, retrieves information of default value and file which config is created
     *
     * @throws CException
     */
    public function getConfigData() {
        $files = CF::findFile('config', $this->group);

        //add backward compatibility
        //TODO: remove folder config in DOCROOT
        if (!is_array($files)) {
            $files = [];
        }
        if (file_exists(DOCROOT . 'config' . DS . $this->group . EXT)) {
            $files[] = DOCROOT . 'config' . DS . $this->group . EXT;
        }
        //reverse ordering to set priority
        if ($files == null) {
            //var_dump(debug_backtrace());
            //throw new CF_Exception('file config '.$group.' not found');
            $files = [];
        }

        $resultFiles = [];
        $resultData = [];
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
                throw new CException('Invalid config format in :file', [':file' => $file]);
            }
            $cfgFiles = $cfg;
            foreach ($cfgFiles as $key => $val) {
                $cfgFiles[$key] = $file;
            }
            $resultFiles = carr::merge($resultFiles, $cfgFiles);
            $resultData = carr::merge($resultData, $cfg);
        }

        //we will flatten the array of result Data
        $result = [];
        $addToResult = function ($key, $value, &$result) use ($resultFiles, $files) {
            $keyParts = explode('.', $key);
            $resultData = [];
            $resultData['key'] = $key;
            $resultData['value'] = $value;
            $resultData['type'] = gettype($value);
            $file = carr::get($resultFiles, carr::first($keyParts));
            $resultData['file'] = $file;
            $parser = new CConfig_Parser();
            foreach ($files as $fileToParse) {
                $comment = $parser->getComment($fileToParse, $key);
                if (strlen($comment) > 0) {
                    break;
                }
            }
            $resultData['comment'] = $comment;
            $result[] = $resultData;
        };
        $flatten = function ($array, $keyPath = '') use (&$flatten, $addToResult, &$result) {
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

    /**
     * Prepend a value onto an array configuration value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function prepend($key, $value) {
        $array = $this->get($key);

        array_unshift($array, $value);

        $this->set($key, $array);
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function push($key, $value) {
        $array = $this->get($key);

        $array[] = $value;

        $this->set($key, $array);
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all() {
        return $this->items;
    }

    public function toArray() {
        return $this->all();
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key) {
        return carr::has($this->items, $key);
    }

    public function offsetExists($key) {
        return $this->has($key);
    }

    public function offsetGet($key) {
        return $this->get($key);
    }

    public function offsetSet($key, $value) {
        $this->set($key, $value);
    }

    public function offsetUnset($key) {
        $this->set($key, null);
    }
}
