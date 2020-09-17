<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 6:24:27 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CQueue_Event_JobFailed {

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
     * The exception that caused the job to fail.
     *
     * @var \Exception
     */
    public $exception;

    /**
     * Create a new event instance.
     *
     * @param  string  $connectionName
     * @param  CQueue_AbstractJob  $job
     * @param  \Exception  $exception
     * @return void
     */
    public function __construct($connectionName, $job, $exception) {
        $this->job = $job;
        $this->exception = $exception;
        $this->connectionName = $connectionName;
    }

}
