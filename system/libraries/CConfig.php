<?php

defined('SYSPATH') or die('No direct access allowed.');

class CConfig implements CInterface_Arrayable, ArrayAccess {
    protected static $instances = [];

    protected $group;

    protected $appCode;

    protected static $repository;

    /**
     * @param string $group
     *
     * @throws CException
     *
     * @return CConfig
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

    public function addAppCode($appCode) {
        $this->appCode = $appCode;

        return $this;
    }

    public function get($key = null, $default = null) {
        $configKey = $key ? $this->group . '.' . $key : $this->group;

        return $this->repository()->get($configKey, $default);
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
            $configKey = $key ? $this->group . '.' . $key : $this->group;
            $config[$key] = $this->repository()->get($configKey, $default);
        }

        return $config;
    }

    public function set($key, $value) {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            $configKey = $key ? $this->group . '.' . $key : $this->group;
            $this->repository()->set($configKey, $value);
        }

        return $this;
    }

    public function refresh() {
        self::manager()->load($this->group);
    }

    /**
     * Flatten the config array, retrieves information of default value and file which config is created.
     *
     * @throws CException
     */
    public function getConfigData() {
        return CConfig_Loader::data($this->group);
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
        return $this->repository()->get($this->group);
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
        return $this->repository()->has($key);
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($key) {
        return $this->has($key);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($key) {
        return $this->get($key);
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($key, $value) {
        $this->set($key, $value);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($key) {
        $this->set($key, null);
    }

    public static function manager() {
        return CConfig_Manager::instance();
    }

    /**
     * @return CConfig_Repository
     */
    public static function repository() {
        return self::manager()->repository();
    }
}
