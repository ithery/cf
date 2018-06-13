<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2018, 10:19:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer {

    public static function storage() {
        return new CServer_Storage();
    }

    public static function phpInfo() {
        return new CServer_PhpInfo();
    }

    public static function getHostname() {
        return gethostname();
    }

    public static function getOs() {
        return PHP_OS;
    }

    public static function getLoadAvg() {
        return sys_getloadavg();
    }

}
