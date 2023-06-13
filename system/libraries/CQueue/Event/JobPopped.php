<?php

class CQueue_Event_JobPopped {
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The job instance.
     *
     * @var null|CQueue_AbstractJob
     */
    public $job;

    /**
     * Create a new event instance.
     *
     * @param string                   $connectionName
     * @param null|CQueue_JobInterface $job
     *
     * @return void
     */
    public function __construct($connectionName, $job) {
        $this->connectionName = $connectionName;
        $this->job = $job;
    }
}
