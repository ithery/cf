<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Raised from the SIGALRM handler, just before the worker is killed.
 */
class CQueue_Event_JobTimedOut {
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The job instance.
     *
     * @var CQueue_JobInterface
     */
    public $job;

    /**
     * @param string              $connectionName
     * @param CQueue_JobInterface $job
     *
     * @return void
     */
    public function __construct($connectionName, $job) {
        $this->connectionName = $connectionName;
        $this->job = $job;
    }
}
