<?php

class CBot_DriverManager {
    /**
     * @var array
     */
    protected static $drivers = [
        CBot_Driver_WebDriver::class
    ];

    /**
     * @var array
     */
    protected $config;

    /**
     * DriverManager constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public static function getAvailableDrivers() {
        return self::$drivers;
    }

    /**
     * @return array
     */
    public static function getAvailableHttpDrivers() {
        return c::collect(self::$drivers)->filter(function ($driver) {
            return is_subclass_of($driver, CBot_DriverAbstract::class);
        })->toArray();
    }

    /**
     * Load a driver by using its name.
     *
     * @param string             $name
     * @param array              $config
     * @param null|CHTTP_Request $request
     *
     * @return mixed|CBot_DriverAbstract
     */
    public static function loadFromName($name, array $config, CHTTP_Request $request = null) {
        /*
        * Use the driver class basename without "Driver" if we're dealing with a
        * DriverInterface object.
        */
        if (class_exists($name) && is_subclass_of($name, CBot_Contract_DriverInterface::class)) {
            $name = preg_replace('#(Driver$)#', '', basename(str_replace('\\', '/', $name)));
        }
        /*
         * Use the driver name constant if we try to load a driver by it's
         * fully qualified class name.
         */
        if (class_exists($name) && is_subclass_of($name, CBot_DriverAbstract::class)) {
            $name = $name::getName();
        }
        if (is_null($request)) {
            $request = c::request();
        }

        foreach (self::getAvailableDrivers() as $driver) {
            /** @var CBot_DriverAbstract $driver */
            $driver = new $driver($request, $config);
            if ($driver->getName() === $name) {
                return $driver;
            }
        }

        return new CBot_Driver_NullDriver($request, []);
    }

    /**
     * @param array $config
     *
     * @return array
     */
    public static function getConfiguredDrivers(array $config) {
        $drivers = [];

        foreach (self::getAvailableHttpDrivers() as $driver) {
            $driver = new $driver(c::request(), $config);
            if ($driver->isConfigured()) {
                $drivers[] = $driver;
            }
        }

        return $drivers;
    }

    /**
     * Append a driver to the list of loadable drivers.
     *
     * @param string $driver   Driver class name
     * @param bool   $explicit Only load this one driver and not any additional (sub)-drivers
     */
    public static function loadDriver($driver, $explicit = false) {
        array_unshift(self::$drivers, $driver);
        if (method_exists($driver, 'loadExtension')) {
            call_user_func([$driver, 'loadExtension']);
        }

        if (method_exists($driver, 'additionalDrivers') && $explicit === false) {
            $additionalDrivers = (array) call_user_func([$driver, 'additionalDrivers']);
            foreach ($additionalDrivers as $additionalDriver) {
                self::loadDriver($additionalDriver);
            }
        }

        self::$drivers = array_unique(self::$drivers);
    }

    /**
     * Remove a driver from the list of loadable drivers.
     *
     * @param string $driver Driver class name
     */
    public static function unloadDriver($driver) {
        foreach (array_keys(self::$drivers, $driver) as $key) {
            unset(self::$drivers[$key]);
        }
    }

    /**
     * Verify service webhook URLs.
     *
     * @param array              $config
     * @param null|CHTTP_Request $request
     *
     * @return bool
     */
    public static function verifyServices(array $config, CHTTP_Request $request = null) {
        if ($request == null) {
            $request = c::request();
        }
        foreach (self::getAvailableHttpDrivers() as $driver) {
            $driver = new $driver($request, $config);
            if ($driver instanceof CBot_Contract_VerifiesServiceInterface && !is_null($driver->verifyRequest($request))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param CHTTP_Request $request
     *
     * @return CBot_DriverAbstract
     */
    public function getMatchingDriver(CHTTP_Request $request) {
        foreach (self::getAvailableDrivers() as $driver) {
            /** @var CBot_DriverAbstract $driver */
            $driver = new $driver($request, $this->config);
            if ($driver->matchesRequest() || $driver->hasMatchingEvent()) {
                return $driver;
            }
        }

        return new CBot_Driver_NullDriver($request, []);
    }
}
