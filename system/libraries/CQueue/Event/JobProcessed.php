<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 6:24:21 AM
 */

class CQueue_Event_JobProcessed {
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The job instance.
     *
     * @var CQueue_AbstractJob
     */
    public $job;

    /**
     * Create a new event instance.
     *
     * @param string              $connectionName
     * @param CQueue_JobInterface $job
     *
     * @return void
     */
    public function __construct($connectionName, $job) {
        $this->job = $job;
        $this->connectionName = $connectionName;
    }
}
