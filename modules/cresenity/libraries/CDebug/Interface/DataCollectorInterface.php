<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 2:36:32 PM
 * @license Ittron Global Teknologi <ittron.co.id>
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
    function collect();

    /**
     * Returns the unique name of the collector
     *
     * @return string
     */
    function getName();
}
