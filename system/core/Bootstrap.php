<?php


date_default_timezone_set('Asia/Jakarta');
//define all constant needed by framework
//we using if because it is maybe already defined in old index.php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
/**
 * Default php file extension.
 */
if (!defined('EXT')) {
    define('EXT', '.php');
}

if (!defined('DOCROOT')) {
    $docroot = realpath(dirname(__FILE__) . DS . '..' . DS . '..' . DS);
    define('DOCROOT', $docroot . DS);
}

if (!defined('CFINDEX')) {
    define('CFINDEX', DOCROOT . 'index.php');
}
is_link(CFINDEX) and chdir(dirname(realpath(__FILE__)));
if (!defined('SYSPATH')) {
    $sysPath = realpath(DOCROOT . 'system');
    define('SYSPATH', $sysPath . DS);
}

if (!defined('MODPATH')) {
    $modPath = realpath(DOCROOT . 'modules');
    define('MODPATH', $modPath . DS);
}

if (!defined('APPPATH')) {
    $appPath = realpath(DOCROOT . 'application');
    define('APPPATH', $appPath . DS);
}

if (!defined('IN_PRODUCTION')) {
    define('IN_PRODUCTION', false);
}

//try to load data domain

//end of constant from index
define('CF_VERSION', '1.8');
define('CF_CODENAME', 'CF1.8');
define('CF_START', microtime(true));

// Test of CF is running in Windows
define('CF_IS_WIN', DIRECTORY_SEPARATOR === '\\');

// CF benchmarks are prefixed to prevent collisions
define('SYSTEM_BENCHMARK', 'system_benchmark');

// Load benchmarking support
require SYSPATH . 'core/CFBenchmark' . EXT;

// Start total_execution
CFBenchmark::start(SYSTEM_BENCHMARK . '_total_execution');

// Start CF Loading
CFBenchmark::start(SYSTEM_BENCHMARK . '_cf_loading');

// Load core files

require SYSPATH . 'core/CFEvent' . EXT;
require SYSPATH . 'core/CFData' . EXT;
require SYSPATH . 'core/CFRouter' . EXT;
require SYSPATH . 'core/CFConsole' . EXT;
require SYSPATH . 'core/CFHTTP' . EXT;
require SYSPATH . 'core/CFDeprecatedTrait' . EXT;
require SYSPATH . 'core/CF' . EXT;
require SYSPATH . 'core/CFConfig' . EXT;

// Prepare the environment
CF::setup();

// End CF Loading
CFBenchmark::stop(SYSTEM_BENCHMARK . '_cf_loading');
if (!defined('CFVSCODE')) {
    if (!CF::isTesting()) {
        if (defined('CFCLI')) {
            CFConsole::execute();
        } else {
            CFHTTP::execute();
        }
    }
}
// stop total_execution
CFBenchmark::stop(SYSTEM_BENCHMARK . '_total_execution');
