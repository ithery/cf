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
     * @param type $sshConfig | optional
     *
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

    public static function getCurrentProcessUser() {
        $processUser = posix_getpwuid(posix_geteuid());

        return carr::get($processUser, 'name');
    }

    public static function getOS() {
        $os = self::config()->get('os');
        if ($os == null) {
            $os = PHP_OS;
        }

        return $os;
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
