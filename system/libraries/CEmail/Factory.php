<?php

class CEmail_Factory {
    protected static $driverMap = [
        'sendgrid' => CEmail_Driver_SendGridDriver::class,
        'mailgun' => CEmail_Driver_MailgunDriver::class,
        'mail' => CEmail_Driver_MailDriver::class,
        'kirimemail' => CEmail_Driver_KirimEmailDriver::class,
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
            if (class_exists('CEmail_Driver_' . cstr::ucfirst(cstr::camel($driver)) . 'Driver')) {
                $class = 'CEmail_Driver_' . cstr::ucfirst(cstr::camel($driver)) . 'Driver';
            }
        }

        if ($class) {
            return new $class($config);
        }

        throw new Exception('Mail driver:' . $driver . ' not found');
    }
}
