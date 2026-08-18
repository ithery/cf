<?php

class CQueue_Event_JobReleasedAfterException {
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The job instance.
     *
     * @var \CQueue_AbstractJob
     */
    public $job;

    /**
     * The number of seconds the job was delayed by.
     *
     * @var null|int
     */
    public $delay;

    /**
     * The exception that caused the release.
     *
     * @var null|Throwable
     */
    public $exception;

    /**
     * Create a new event instance.
     *
     * @param string               $connectionName
     * @param \CQueue_JobInterface $job
     * @param null|int             $delay
     * @param null|Throwable       $exception
     *
     * @return void
     */
    public function __construct($connectionName, $job, $delay = null, $exception = null) {
        $this->job = $job;
        $this->connectionName = $connectionName;
        $this->delay = $delay;
        $this->exception = $exception;
    }
}
