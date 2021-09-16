<?php

class CEmail_Factory {
    protected static $driverMap = [
        'sendgrid' => CEmail_Driver_SendGridDriver::class,
    ];

    /**
     * @param string $driver
     *
     * @return CEmail_DriverAbstract
     */
    public static function createDriver(CEmail_Config $config) {
        $driver = $config->getDriver();
        $class = carr::get(static::$driverMap, $driver);
        if (!$class) {
            if (class_exists('CEmail_Driver_' . cstr::camel($driver) . 'Driver')) {
                $class = 'CEmail_Driver_' . cstr::camel($driver) . 'Driver';
            }
        }

        if ($class) {
            return new $class($config);
        }
        return null;
    }
}
