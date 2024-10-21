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
     * Create a new event instance.
     *
     * @param string               $connectionName
     * @param \CQueue_JobInterface $job
     *
     * @return void
     */
    public function __construct($connectionName, $job) {
        $this->job = $job;
        $this->connectionName = $connectionName;
    }
}
