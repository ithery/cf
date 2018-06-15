<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2018, 10:19:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer {

    public static function storage() {
        return CServer_Storage::instance();
    }

    public static function php() {
        return CServer_Php::instance();
    }

    public static function database() {
        return CServer_Database::instance();
    }

    public static function memory() {
        return CServer_Memory::instance();
    }

    public static function system() {
        return CServer_System::instance();
    }

    public static function error() {
        return CServer_Error::instance();
    }

    public static function command() {
        return CServer_Command::instance();
    }

    public static function config() {
        return CServer_Config::instance();
    }

    public static function phpInfo() {
        return new CServer_PhpInfo();
    }

    public static function getHostname() {
        return gethostname();
    }

    public static function getOS() {
        $os = self::config()->get('os');
        if ($os == null) {
            $os = PHP_OS;
        }
        return $os;
    }

    public static function getLoadAvg() {
        return sys_getloadavg();
    }

    /**
     * 
     * @return CServer_System_Device
     */
    public static function createDevice($deviceName) {
        $class = 'CServer_Device_' . $deviceName;
        return new $class();
    }

    /**
     * 
     * @return CServer_System_Device_Cpu
     */
    public static function createDeviceCpu() {
        return self::createDevice('Cpu');
    }

    /**
     * 
     * @return CServer_System_Device_Disk
     */
    public static function createDeviceDisk() {
        return self::createDevice('Disk');
    }

}
