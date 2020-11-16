<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite {

    use CDevSuite_Trait_ConsoleTrait,
        CDevSuite_Trait_WindowsTrait,
        CDevSuite_Trait_LinuxTrait;

    /**
     *
     * @var CDevSuite_Configuration
     */
    protected static $configuration;

    /**
     *
     * @var CDevSuite_Filesystem
     */
    protected static $filesystem;

    /**
     *
     * @var CDevSuite_CommandLine
     */
    protected static $commandLine;

    /**
     *
     * @var CDevSuite_Nginx
     */
    protected static $nginx;

    /**
     *
     * @var CDevSuite_PackageManager
     */
    protected static $packageManager;

    /**
     *
     * @var CDevSuite_ServiceManager
     */
    protected static $serviceManager;

    /**
     *
     * @var CDevSuite_Site
     */
    protected static $site;

    /**
     *
     * @var CDevSuite_PhpFpm
     */
    protected static $phpFpm;

    public static function bootstrap() {
        CDevSuite_Bootstrap::instance()->bootstrap();
    }

    public static function homePath() {
        $path = $_SERVER['HOME'] . DS . '.config' . DS . 'devsuite' . DS;
        return str_replace('\\', '/', $path);
    }

    public static function legacyHomePath() {
        $path = $_SERVER['HOME'] . DS . '.devsuite' . DS;
        return str_replace('\\', '/', $path);
    }

    public static function binPath() {
        $binPath = DOCROOT . '.bin' . DS . 'devsuite' . DS;
        if (CServer::getOS() == CServer::OS_WINNT) {
            $binPath .= 'win' . DS;
        }
        $binPath = str_replace('\\', '/', $binPath);

        return $binPath;
    }

    public static function serverPath() {
        $path = DOCROOT . 'devsuite.php';
        return str_replace('\\', '/', $path);
    }

    public static function stubsPath() {
        $path = DOCROOT . 'system' . DS . 'data' . DS . 'devsuite' . DS . 'stubs' . DS;
        return str_replace('\\', '/', $path);
    }

    public static function scriptsPath() {
        $path = DOCROOT . 'system' . DS . 'data' . DS . 'devsuite' . DS . 'scripts' . DS;
        return str_replace('\\', '/', $path);
    }

    public static function configuration() {
        if (static::$configuration == null) {
            switch (CServer::getOS()) {
                case CServer::OS_LINUX:
                    static::$configuration = new CDevSuite_Linux_Configuration();
                    break;
                case CServer::OS_WINNT:
                    static::$configuration = new CDevSuite_Windows_Configuration();
                    break;
            }
        }
        return static::$configuration;
    }

    public static function filesystem() {
        if (static::$filesystem == null) {
            switch (CServer::getOS()) {
                case CServer::OS_LINUX:
                    static::$filesystem = new CDevSuite_Linux_Filesystem();
                    break;
                case CServer::OS_WINNT:
                    static::$filesystem = new CDevSuite_Windows_Filesystem();
                    break;
                case CServer::OS_DARWIN:
                    static::$filesystem = new CDevSuite_Mac_Filesystem();
                    break;
            }
        }
        return static::$filesystem;
    }

    public static function commandLine() {
        if (static::$commandLine == null) {
            switch (CServer::getOS()) {
                case CServer::OS_LINUX:
                    static::$commandLine = new CDevSuite_Linux_CommandLine();
                    break;
                case CServer::OS_WINNT:
                    static::$commandLine = new CDevSuite_Windows_CommandLine();
                    break;
                case CServer::OS_DARWIN:
                    static::$commandLine = new CDevSuite_Mac_CommandLine();
                    break;

                }
        }
        return static::$commandLine;
    }

    public static function site() {
        if (static::$site == null) {
            switch (CServer::getOS()) {
                case CServer::OS_LINUX:
                    static::$site = new CDevSuite_Linux_Site();
                    break;
                case CServer::OS_WINNT:
                    static::$site = new CDevSuite_Windows_Site();
                    break;
                case CServer::OS_DARWIN:
                    static::$site = new CDevSuite_Mac_Site();
                    break;
            }
        }
        return static::$site;
    }

    public static function nginx() {
        if (static::$nginx == null) {
            switch (CServer::getOS()) {
                case CServer::OS_LINUX:
                    static::$nginx = new CDevSuite_Linux_Nginx();
                    break;
                case CServer::OS_WINNT:
                    static::$nginx = new CDevSuite_Windows_Nginx();
                    break;
                case CServer::OS_DARWIN:
                    static::$nginx = new CDevSuite_Mac_Nginx();
                    break;
            }
        }
        return static::$nginx;
    }

    public static function phpFpm() {
        if (static::$phpFpm == null) {
            switch (CServer::getOS()) {
                case CServer::OS_LINUX:
                    static::$phpFpm = new CDevSuite_Linux_PhpFpm();
                    break;
                case CServer::OS_WINNT:
                    static::$phpFpm = new CDevSuite_Windows_PhpFpm();
                    break;
                case CServer::OS_DARWIN:
                    static::$phpFpm = new CDevSuite_Mac_PhpFpm();
                    break;
            }
        }
        return static::$phpFpm;
    }

    public static function packageManager() {
        if (static::$packageManager == null) {
            switch (CServer::getOS()) {
                case CServer::OS_LINUX:
                    static::$packageManager = c::collect([
                                CDevSuite_PackageManager_Apt::class,
                                CDevSuite_PackageManager_AptGet::class,
                                CDevSuite_PackageManager_Dnf::class,
                                CDevSuite_PackageManager_Pacman::class,
                                CDevSuite_PackageManager_Yum::class,
                                CDevSuite_PackageManager_PackageKit::class,
                                CDevSuite_PackageManager_Eopkg::class,
                            ])->map(function($className) {
                                return CDevSuite_Helper::resolve($className);
                            })->first(static function ($pm) {
                        return $pm->isAvailable();
                    }, static function () {
                        throw new DomainException("No compatible package manager found.");
                    });
                    break;
            }
        }
        return static::$packageManager;
    }

    public static function serviceManager() {
        if (static::$serviceManager == null) {
            switch (CServer::getOS()) {
                case CServer::OS_LINUX:
                    static::$serviceManager = c::collect([
                                CDevSuite_ServiceManager_LinuxService::class,
                                CDevSuite_ServiceManager_Systemd::class,
                            ])->map(function($className) {
                                return CDevSuite_Helper::resolve($className);
                            })->first(static function ($pm) {
                        return $pm->isAvailable();
                    }, static function () {
                        throw new DomainException("No compatible service manager found.");
                    });
                    break;
            }
        }
        return static::$serviceManager;
    }

    public static function user() {
        if (!isset($_SERVER['SUDO_USER'])) {
            return $_SERVER['USER'];
        }

        return $_SERVER['SUDO_USER'];
    }

    /**
     * Get the user's group
     */
    public static function group() {
        switch (CServer::getOS()) {
            case CServer::OS_LINUX:
                if (!isset($_SERVER['SUDO_USER'])) {
                    return exec('id -gn ' . $_SERVER['USER']);
                }

                return exec('id -gn ' . $_SERVER['SUDO_USER']);
            default:
                return '';
        }
    }

    /**
     * Verify that the script is currently running as "sudo".
     *
     * @return void
     */
    public static function shouldBeSudo() {
        if (!isset($_SERVER['SUDO_USER'])) {
            throw new Exception('This command must be run with sudo.');
        }
    }

    public static function staticPrefix() {
        return '41c270e4-5535-4daa-b23e-c269744c2f45';
    }

}
