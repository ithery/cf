<?php

class CDaemon_Event_WorkerProcessRestarting {
    /**
     * The worker process instance.
     *
     * @var \CDaemon_WorkerProcess
     */
    public $process;

    /**
     * Create a new event instance.
     *
     * @param \CDaemon_WorkerProcess $process
     *
     * @return void
     */
    public function __construct(CDaemon_WorkerProcess $process) {
        $this->process = $process;
    }
}
