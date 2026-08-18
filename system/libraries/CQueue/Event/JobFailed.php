<?php

defined('SYSPATH') or die('No direct access allowed.');

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
     * @param string             $connectionName
     * @param CQueue_AbstractJob $job
     * @param \Exception         $exception
     *
     * @return void
     */
    public function __construct($connectionName, $job, $exception) {
        $this->job = $job;
        $this->exception = $exception;
        $this->connectionName = $connectionName;
    }
}
