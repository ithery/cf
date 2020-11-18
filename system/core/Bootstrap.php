<?php

defined('SYSPATH') OR die('No direct access allowed.');

define('CF_VERSION', '1.0');
define('CF_CODENAME', 'CF1.0');

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
require SYSPATH . 'core/utf8' . EXT;
require SYSPATH . 'core/CFEvent' . EXT;
require SYSPATH . 'core/CFData' . EXT;
require SYSPATH . 'core/CFRouter' . EXT;
require SYSPATH . 'core/CFConsole' . EXT;
require SYSPATH . 'core/CFHTTP' . EXT;
require SYSPATH . 'core/CFDeprecatedTrait' . EXT;
require SYSPATH . 'core/CF' . EXT;

// Prepare the environment
CF::setup();


// End CF Loading
CFBenchmark::stop(SYSTEM_BENCHMARK . '_cf_loading');


if (defined('CFCLI')) {
    CFConsole::execute();
} else {
    CFHTTP::execute();
}

// stop total_execution
CFBenchmark::stop(SYSTEM_BENCHMARK . '_total_execution');
