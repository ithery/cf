<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * CF process control file, loaded by the front controller.
 * 
 * $Id: Bootstrap.php 4409 2009-06-06 00:48:26Z zombor $
 *
 * @package    Core
 * @author     CF Team
 * @copyright  (c) 2007 CF Team
 * @license    http://kohanaphp.com/license.html
 */

define('CF_VERSION',  '1.0');
define('CF_CODENAME', 'CF1.0');

// Test of CF is running in Windows
define('CF_IS_WIN', DIRECTORY_SEPARATOR === '\\');

// CF benchmarks are prefixed to prevent collisions
define('SYSTEM_BENCHMARK', 'system_benchmark');

// Load benchmarking support
require SYSPATH.'core/CFBenchmark'.EXT;

// Start total_execution
CFBenchmark::start(SYSTEM_BENCHMARK.'_total_execution');

// Start kohana_loading
CFBenchmark::start(SYSTEM_BENCHMARK.'_cf_loading');

// Load core files
require SYSPATH.'core/utf8'.EXT;
require SYSPATH.'core/CFEvent'.EXT;
require SYSPATH.'core/CFData'.EXT;
require SYSPATH.'core/CFRouter'.EXT;
require SYSPATH.'core/CFConsole'.EXT;
require SYSPATH.'core/CFDeprecatedTrait'.EXT;
require SYSPATH.'core/CF'.EXT;

// Prepare the environment
CF::setup();

// End kohana_loading
CFBenchmark::stop(SYSTEM_BENCHMARK.'_cf_loading');

// Start system_initialization
CFBenchmark::start(SYSTEM_BENCHMARK.'_system_initialization');

// Prepare the system
CFEvent::run('system.ready');

// Determine routing
CFEvent::run('system.routing');

// End system_initialization
CFBenchmark::stop(SYSTEM_BENCHMARK.'_system_initialization');

// Make the magic happen!
CFEvent::run('system.execute');

// Clean up and exit
CFEvent::run('system.shutdown');
