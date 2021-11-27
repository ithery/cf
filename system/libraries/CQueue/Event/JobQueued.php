<?php
class CQueue_Event_JobQueued {
    /**
     * The connection name.
     *
     * @var string
     */
    public $connectionName;

    /**
     * The job ID.
     *
     * @var null|string|int
     */
    public $id;

    /**
     * The job instance.
     *
     * @var \Closure|string|object
     */
    public $job;

    /**
     * Create a new event instance.
     *
     * @param string                 $connectionName
     * @param null|string|int        $id
     * @param \Closure|string|object $job
     *
     * @return void
     */
    public function __construct($connectionName, $id, $job) {
        $this->connectionName = $connectionName;
        $this->id = $id;
        $this->job = $job;
    }
}
