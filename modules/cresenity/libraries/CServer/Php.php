<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 12:58:56 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Php {

    protected static $instance;

    public static function instance() {
        if (self::$instance == null) {
            return new CServer_Php();
        }
        return self::$instance;
    }

    /**
     * Gets the current PHP version
     * @return string      
     */
    public static function getVersion() {
        return phpversion();
    }

    /**
     * Gets the PHP extension version
     * @param string $extName <p>
     * An extension name.
     * </p>
     * @return string returns the version of that
     * extension, or <b>FALSE</b> if there is no version information associated or
     * the extension isn't enabled.
     */
    public static function getExtVersion($extName) {
        return phpversion($extName);
    }

    public static function getAllIniConfiguration() {
        return ini_get_all();
    }

    public static function getAllIniConfigurationExt($extName) {
        return ini_get_all($extName);
    }

    /**
     * Gets the value of a configuration option
     * @param string $varname <p>
     * The configuration option name.
     * </p>
     * @return string the value of the configuration option as a string on success, or an
     * empty string for null values. Returns <b>FALSE</b> if the
     * configuration option doesn't exist.
     */
    public static function getIniConfiguration($varName) {
        return ini_get($varName);
    }

    /**
     * Retrieve a path to the loaded php.ini file
     * @return string The loaded <i>php.ini</i> path, or <b>FALSE</b> if one is not loaded.
     */
    public static function getIniLoadedFile() {
        return php_ini_loaded_file();
    }

    /**
     * Returns the type of interface between web server and PHP
     * @return string the interface type, as a lowercase string.
     */
    public static function getSapiName() {
        return php_sapi_name();
    }

    /**
     * Returns directory path used for temporary files
     * @return string the path of the temporary directory.
     */
    public static function getTempDir() {
        return sys_get_temp_dir();
    }

    /**
     * Gets the name of the owner of the current PHP script
     * @return string the username as a string.
     */
    public static function getCurrentUser() {
        return get_current_user();
    }

}
