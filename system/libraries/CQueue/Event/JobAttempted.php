<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Raised after every attempt at a job, successful or not.
 *
 * Unlike JobProcessed and JobFailed this one always fires, so it is the hook to
 * use for anything that has to run whatever the outcome was.
 */
class CQueue_Event_JobAttempted {
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
     * The exception the attempt ended with, if any.
     *
     * @var null|Throwable
     */
    public $exceptionOccurred;

    /**
     * @param string              $connectionName
     * @param CQueue_JobInterface $job
     * @param null|Throwable      $exceptionOccurred
     *
     * @return void
     */
    public function __construct($connectionName, $job, $exceptionOccurred = null) {
        $this->connectionName = $connectionName;
        $this->job = $job;
        $this->exceptionOccurred = $exceptionOccurred;
    }

    /**
     * Determine if the job completed without an exception.
     *
     * @return bool
     */
    public function successful() {
        return $this->exceptionOccurred === null && !$this->job->hasFailed();
    }
}
