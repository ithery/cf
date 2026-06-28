<?php

defined('SYSPATH') or die('No direct access allowed.');

class CServer {
    const OS_WINNT = 'WINNT';

    const OS_LINUX = 'Linux';

    const OS_DARWIN = 'Darwin';

    /**
     * Array expression search.
     */
    const ARRAY_EXP = '/^return array \([^;]*\);$/';

    /**
     * @var array
     */
    protected static $serverInstances = [];

    /**
     * @param null|CRemote_SSH|CRemote_SSH_Config $sshConfig
     *
     * @return CServer_Server
     */
    public static function server($sshConfig = null) {
        $key = 'localhost';
        if ($sshConfig instanceof CRemote_SSH) {
            $key = $sshConfig->getHost();
        } elseif ($sshConfig instanceof CRemote_SSH_Config) {
            $key = $sshConfig->getConnectionHost() . ':' . ($sshConfig->getPort() ?: 22) . ':' . ($sshConfig->getUsername() ?: 'root');
        }

        if (!isset(self::$serverInstances[$key])) {
            self::$serverInstances[$key] = new CServer_Server($sshConfig);
        }

        return self::$serverInstances[$key];
    }

    /**
     * @param null|CRemote_SSH|CRemote_SSH_Config $sshConfig
     *
     * @return CServer_Storage
     */
    public static function storage($sshConfig = null) {
        return self::server($sshConfig)->storage();
    }

    /**
     * @param null|CRemote_SSH|CRemote_SSH_Config $sshConfig
     *
     * @return CServer_Php
     */
    public static function php($sshConfig = null) {
        return self::server($sshConfig)->php();
    }

    public static function database() {
        return CServer_Database::instance();
    }

    /**
     * @param null|CRemote_SSH|CRemote_SSH_Config $sshConfig
     *
     * @return CServer_Memory
     */
    public static function memory($sshConfig = null) {
        return self::server($sshConfig)->memory();
    }

    /**
     * @param null|CRemote_SSH|CRemote_SSH_Config $sshConfig
     *
     * @return CServer_System
     */
    public static function system($sshConfig = null) {
        return self::server($sshConfig)->system();
    }

    public static function error() {
        return CServer_Error::instance();
    }

    /**
     * @param null|CRemote_SSH|CRemote_SSH_Config $sshConfig
     *
     * @return CServer_Config
     */
    public static function config($sshConfig = null) {
        return self::server($sshConfig)->config();
    }

    /**
     * @return CServer_PhpInfo
     */
    public static function phpInfo() {
        return self::server()->phpInfo();
    }

    public static function getHostname() {
        return gethostname();
    }

    public static function getCurrentProcessUser() {
        $processUser = posix_getpwuid(posix_geteuid());

        return carr::get($processUser, 'name');
    }

    /**
     * @return string
     */
    public static function getOS() {
        return self::server()->getOS();
    }

    public static function isWindows() {
        return strtoupper(substr(PHP_OS, 0, 3)) == 'WIN';
    }

    public static function getLoadAvg() {
        return sys_getloadavg();
    }

    public static function isProcOpenDisabled() {
        $isDisabled = false;
        if (!function_exists('proc_open')) {
            $isDisabled = true;
        }
        $process = null;

        try {
            $descriptorspec = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
            $pipes = [];
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

    /**
     * @param mixed $options
     *
     * @return \CServer_Service_Beanstalkd
     */
    public static function createBeanstalkd($options = []) {
        return new CServer_Service_Beanstalkd($options);
    }

    public static function isNpmInstalled() {
        exec('npm -v', $output, $exitCode);

        return $exitCode === 0;
    }

    public static function isComposerInstalled() {
        exec('composer -v', $output, $exitCode);

        return $exitCode === 0;
    }

    public static function dns() {
        return new CServer_Dns();
    }

    public static function nodeJs($nodePath = null) {
        return new CServer_NodeJs($nodePath);
    }

    public static function browsershot($url = '', $deviceEmulate = false) {
        return new CServer_Browsershot($url, $deviceEmulate);
    }

    public static function runSMTPServer($options = []) {
        return CServer_SMTP_ServerManager::instance()->run($options);
    }

    /**
     * @return CServer_OS
     */
    public static function os() {
        return new CBase_ForwarderStaticClass(CServer_OS::class);
    }
}
