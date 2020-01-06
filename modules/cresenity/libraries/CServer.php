<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2018, 10:19:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer {

    public static function storage($sshConfig = null) {
        return CServer_Storage::instance($sshConfig);
    }

    public static function php($sshConfig = null) {
        return CServer_Php::instance($sshConfig);
    }

    public static function database() {
        return CServer_Database::instance();
    }

    public static function memory($sshConfig = null) {
        return CServer_Memory::instance($sshConfig);
    }

    public static function system($sshConfig = null) {
        return CServer_System::instance($sshConfig);
    }

    public static function error() {
        return CServer_Error::instance();
    }

    /**
     * 
     * @param type $sshConfig | optional
     * @return CServer_Command
     */
    public static function command($sshConfig = null) {
        return CServer_Command::instance($sshConfig);
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
     * @return CServer_Device_Cpu
     */
    public static function createDeviceCpu() {
        return self::createDevice('Cpu');
    }

    /**
     * 
     * @return CServer_Device_Disk
     */
    public static function createDeviceDisk() {
        return self::createDevice('Disk');
    }

    public static function isProcOpenDisabled() {
        $isDisabled = false;
        if (!function_exists("proc_open")) {
            $isDisabled = true;
        }
        $process = null;
        try {
            $descriptorspec = array(0 => array("pipe", "r"), 1 => array("pipe", "w"), 2 => array("pipe", "w"));
            $pipes = array();
            $process = @proc_open('hostname', $descriptorspec, $pipes);
        } catch (Exception $ex) {
            $isDisabled = true;
        }

        if (is_resource($process)) {
            proc_terminate($process);
        } else {
            $isDisabled = true;
        }


        return $isDisabled;
    }

}
