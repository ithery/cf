<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 15, 2019, 7:57:09 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CSocialLogin {

    public static function driver($driverName, $options = []) {
        $driverManager = new CSocialLogin_DriverManager();
        return $driverManager->setConfig($options)->driver($driverName);
    }

}
