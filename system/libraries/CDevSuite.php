<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite {

    use CDevSuite_Trait_ConsoleTrait;

    protected static $linuxRequirements;

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

    public static function homePath() {
        return DOCROOT . 'data/devsuite/';
    }

    public static function linuxRequirements() {
        if (static::$linuxRequirements == null) {
            static::$linuxRequirements = new CDevSuite_LinuxRequirements();
        }
        return static::$linuxRequirements;
    }

    public static function configuration() {
        if (static::$configuration == null) {
            switch (CServer::getOS()) {
                case CServer::OS_LINUX:
                    static::$configuration = new CDevSuite_Linux_Configuration();
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
            }
        }
        return static::$commandLine;
    }

    public static function nginx() {
        if (static::$nginx == null) {
            switch (CServer::getOS()) {
                case CServer::OS_LINUX:
                    static::$nginx = new CDevSuite_Linux_Nginx();
                    break;
            }
        }
        return static::$nginx;
    }

    public static function packageManager() {
        if (static::$packageManager == null) {
            switch (CServer::getOS()) {
                case CServer::OS_LINUX:
                    static::$packageManager = c::collect([
                                CDevSuite_PackageManager_Apt::class,
                                CDevSuite_PackageManager_Dnf::class,
                                CDevSuite_PackageManager_Pacman::class,
                                CDevSuite_PackageManager_Yum::class,
                                CDevSuite_PackageManager_PackageKit::class,
                                CDevSuite_PackageManager_Eopkg::class,
                            ])->first(static function ($pm) {
                        return CDevSuite_Helper::resolve($pm)->isAvailable();
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
                            ])->first(static function ($pm) {
                        return CDevSuite_Helper::resolve($pm)->isAvailable();
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

    public static function serverPath() {
        return DOCROOT;
    }

    public static function stubsPath() {
        return DOCROOT . 'system' . DS . 'data' . DS . 'devsuite' . DS . 'stubs' . DS;
    }

}
