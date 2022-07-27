<?php

class CDaemon_Supervisor_Listener_ForgetJobTimer {
    /**
     * The stopwatch instance.
     *
     * @var \CDaemon_Supervisor_Stopwatch
     */
    public $watch;

    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct() {
        $this->watch = CDaemon_Supervisor::stopwatch();
    }

    /**
     * Handle the event.
     *
     * @param \CQueue_Event_JobExceptionOccurred|\CQueue_Event_JobFailed $event
     *
     * @return void
     */
    public function handle($event) {
        $this->watch->forget($event->job->getJobId());
    }
}
