<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 15, 2019, 7:57:09 PM
 */
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
