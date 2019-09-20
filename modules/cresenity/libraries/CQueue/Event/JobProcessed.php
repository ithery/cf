<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 6:24:21 AM
 * @license Ittron Global Teknologi <ittron.co.id>
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
     * @param  string  $connectionName
     * @param  CQueue_JobInterface  $job
     * @return void
     */
    public function __construct($connectionName, $job) {
        $this->job = $job;
        $this->connectionName = $connectionName;
    }

}
