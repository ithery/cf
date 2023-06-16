<?php

defined('SYSPATH') or die('No direct access allowed.');

use DebugBar\DataCollector\TimeDataCollector;

/**
 * Collects info about the request duration as well as providing
 * a way to log duration of any operations.
 */
class CDebug_DataCollector_TimeDataCollector extends TimeDataCollector {
}
