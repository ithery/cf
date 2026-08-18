<?php

defined('SYSPATH') or die('No direct access allowed.');

class CSocialLogin {
    /**
     * @param string $driverName
     * @param array  $options
     *
     * @return CSocialLogin_AbstractProviderInterface
     */
    public static function driver($driverName, $options = []) {
        $driverManager = new CSocialLogin_DriverManager();

        return $driverManager->setConfig($options)->driver($driverName);
    }
}
