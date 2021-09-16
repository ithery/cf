<?php

/**
 * Website application directory. This directory should contain your application
 * configuration, controllers, models, views, and other resources.
 *
 * This path can be absolute or relative to this file.
 */
$cf_application = 'application';

/**
 * CF modules directory. This directory should contain all the modules used
 * by your application. Modules are enabled and disabled by the application
 * configuration file.
 *
 * This path can be absolute or relative to this file.
 */
$cf_modules = 'modules';

/**
 * CF system directory. This directory should contain the core/ directory,
 * and the resources you included in your download of CF.
 *
 * This path can be absolute or relative to this file.
 */
$cf_system = 'system';

/**
 * Test to make sure that CF is running on PHP 5.2 or newer. Once you are
 * sure that your environment is compatible with CF, you can comment this
 * line out. When running an application on a new server, uncomment this line
 * to check the PHP version quickly.
 */
version_compare(PHP_VERSION, '7.3', '<') and exit('CF requires PHP 7.3 or newer.');

/**
 * Set the error reporting level. Unless you have a special need, E_ALL is a
 * good level for error reporting.
 */
error_reporting(E_ALL & ~E_STRICT ^ E_DEPRECATED);

/**
 * Turning off display_errors will effectively disable CF error display
 * and logging. You can turn off CF errors in application/config/config.php
 */
ini_set('display_errors', true);

//
// DO NOT EDIT BELOW THIS LINE, UNLESS YOU FULLY UNDERSTAND THE IMPLICATIONS.
// ----------------------------------------------------------------------------
// $Id: index.php 3915 2009-01-20 20:52:20Z zombor $
//
if (isset($_FILES) && is_array($_FILES)) {
    foreach ($_FILES as $k => $v) {
        if (isset($v['name'])) {
            $t = $v['name'];

            if (!is_array($t)) {
                $t = [$t];
            }
            foreach ($t as $g) {
                if (!is_array($g)) {
                    $ext = pathinfo($g, PATHINFO_EXTENSION);
                    if (strlen($ext) > 3) {
                        $ext = substr($ext, 0, 3);
                    }
                    if (in_array($ext, ['php', 'sh', 'htm'])) {
                        die('Not Allowed X_X');
                    }
                } else {
                    foreach ($g as $h) {
                        if (!is_array($h)) {
                            $ext = pathinfo($h, PATHINFO_EXTENSION);
                            if (strlen($ext) > 3) {
                                $ext = substr($ext, 0, 3);
                            }
                            if (in_array($ext, ['php', 'sh', 'htm'])) {
                                die('Not Allowed X_X');
                            }
                        }
                    }
                }
            }
        }
    }
}

$cfPathInfo = pathinfo(__FILE__);
// Define the front controller name and docroot
define('DOCROOT', realpath($cfPathInfo['dirname'] . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR);
define('KOHANA', $cfPathInfo['basename']);
chdir(DOCROOT);

//we get app code from here
$file = DOCROOT . 'data' . DIRECTORY_SEPARATOR . 'domain' . DIRECTORY_SEPARATOR;
if (PHP_SAPI === 'cli') {
    // Command line requires a bit of hacking
    if (isset($_SERVER['argv'][2])) {
        $domain = $_SERVER['argv'][2];
    }
} else {
    $domain = $_SERVER['SERVER_NAME'];
}
$file .= $domain . '.php';

if (file_exists($file)) {
    $content = file_get_contents($file);
    $data = json_decode($content, true);
    $app_code = '';
    if (isset($data['app_code'])) {
        $app_code = $data['app_code'];
    }

    $cf_application = 'application' . DIRECTORY_SEPARATOR . $app_code;
}

// If kohana folders are relative paths, make them absolute.
//$cf_application = file_exists($cf_application) ? $cf_application : DOCROOT . $cf_application;
//$cf_modules = file_exists($cf_modules) ? $cf_modules : DOCROOT . $cf_modules;
//$cf_system = file_exists($cf_system) ? $cf_system : DOCROOT . $cf_system;
$cf_application = DOCROOT . $cf_application;
$cf_modules = DOCROOT . $cf_modules;
$cf_system = DOCROOT . $cf_system;

// Define application and system paths
define('APPPATH', str_replace('\\', '/', realpath($cf_application)) . '/');
define('MODPATH', str_replace('\\', '/', realpath($cf_modules)) . '/');
define('SYSPATH', str_replace('\\', '/', realpath($cf_system)) . '/');

// Clean up
unset($cf_application, $cf_modules, $cf_system);

require DOCROOT . 'system/core/Bootstrap.php';
