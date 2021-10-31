<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 2:36:32 PM
 */

/**
 * DataCollector Interface
 */
interface CDebug_Interface_DataCollectorInterface {
    /**
     * Called by the DebugBar when data needs to be collected
     *
     * @return array Collected data
     */
    public function collect();

    /**
     * Returns the unique name of the collector
     *
     * @return string
     */
    public function getName();
}
