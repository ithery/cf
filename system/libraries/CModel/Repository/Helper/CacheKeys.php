<?php

/**
 * Class CacheKeys.
 */
class CModel_Repository_Helper_CacheKeys {
    /**
     * @var string
     */
    protected static $storeFile = 'repository-cache-keys.json';

    /**
     * @var array
     */
    protected static $keys = null;

    /**
     * @param $group
     * @param $key
     *
     * @return void
     */
    public static function putKey($group, $key) {
        self::loadKeys();

        self::$keys[$group] = self::getKeys($group);

        if (!in_array($key, self::$keys[$group])) {
            self::$keys[$group][] = $key;
        }

        self::storeKeys();
    }

    /**
     * @return array|mixed
     */
    public static function loadKeys() {
        if (!is_null(self::$keys) && is_array(self::$keys)) {
            return self::$keys;
        }

        $file = self::getFileKeys();

        if (!file_exists($file)) {
            self::storeKeys();
        }

        $content = file_get_contents($file);
        self::$keys = json_decode($content, true);

        return self::$keys;
    }

    /**
     * @return string
     */
    public static function getFileKeys() {
        $file = DOCROOT . 'temp' . DS . CF::appCode() . DS . 'model' . DS . 'repository' . DS . self::$storeFile;

        return $file;
    }

    /**
     * @return int
     */
    public static function storeKeys() {
        $file = self::getFileKeys();
        self::$keys = is_null(self::$keys) ? [] : self::$keys;
        $content = json_encode(self::$keys);

        return file_put_contents($file, $content);
    }

    /**
     * @param $group
     *
     * @return array|mixed
     */
    public static function getKeys($group) {
        self::loadKeys();
        self::$keys[$group] = isset(self::$keys[$group]) ? self::$keys[$group] : [];

        return self::$keys[$group];
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters) {
        $instance = new static();

        return call_user_func_array([
            $instance,
            $method
        ], $parameters);
    }

    /**
     * @param $method
     * @param $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        $instance = new static();

        return call_user_func_array([
            $instance,
            $method
        ], $parameters);
    }
}
