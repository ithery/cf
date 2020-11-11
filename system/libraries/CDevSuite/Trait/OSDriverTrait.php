<?php

/**
 * Description of HaveOSDriverTrait
 *
 * @author Hery
 */
trait CDevSuite_Trait_OSDriverTrait {

    protected static $driver;

    public function __call($name, $arguments) {
        return call_user_func([$this->driver(), $name], $arguments);
    }

    public static function __callStatic($name, $arguments) {
        return call_user_func([static::driver(), $name], $arguments);
    }

    protected static function driver() {
        if (static::$driver == null) {
            static::$driver = static::createDriver();
        }
        return static::$driver;
    }

}
