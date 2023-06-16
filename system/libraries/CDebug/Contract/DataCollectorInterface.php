<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * DataCollector Interface.
 */
interface CDebug_Contract_DataCollectorInterface {
    /**
     * Called by the DebugBar when data needs to be collected.
     *
     * @return array Collected data
     */
    public function collect();

    /**
     * Returns the unique name of the collector.
     *
     * @return string
     */
    public function getName();
}
