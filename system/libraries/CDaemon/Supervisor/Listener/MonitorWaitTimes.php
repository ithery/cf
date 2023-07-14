<?php

use Carbon\CarbonImmutable;

class CDaemon_Supervisor_Listener_MonitorWaitTimes {
    /**
     * The metrics repository implementation.
     *
     * @var \CDaemon_Supervisor_Contract_MetricsRepositoryInterface
     */
    public $metrics;

    /**
     * The time at which we last checked if monitoring was due.
     *
     * @var \Carbon\CarbonImmutable
     */
    public $lastMonitored;

    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct() {
        $this->metrics = CDaemon::supervisor()->metricsRepository();
    }

    /**
     * Handle the event.
     *
     * @param \CDaemon_Supervisor_Event_SupervisorLooped $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_SupervisorLooped $event) {
        if (!$this->dueToMonitor()) {
            return;
        }

        // Here we will calculate the wait time in seconds for each of the queues that
        // the application is working. Then, we will filter the results to find the
        // queues with the longest wait times and raise events for each of these.
        $results = CDaemon_Supervisor::waitTimeCalculator()->calculate();

        $long = c::collect($results)->filter(function ($wait, $queue) {
            return $wait > (CF::config("daemon.supervisor.waits.{$queue}") ?? 60);
        });

        // Once we have determined which queues have long wait times we will raise the
        // events for each of the queues. We'll need to separate the connection and
        // queue names into their own strings before we will fire off the events.
        $long->each(function ($wait, $queue) {
            list($connection, $queue) = explode(':', $queue, 2);

            c::event(new CDaemon_Supervisor_Event_LongWaitDetected($connection, $queue, $wait));
        });
    }

    /**
     * Determine if monitoring is due.
     *
     * @return bool
     */
    protected function dueToMonitor() {
        // We will keep track of the amount of time between attempting to acquire the
        // lock to monitor the wait times. We only want a single supervisor to run
        // the checks on a given interval so that we don't fire too many events.
        if (!$this->lastMonitored) {
            $this->lastMonitored = CarbonImmutable::now();
        }

        if (!$this->timeToMonitor()) {
            return false;
        }

        // Next we will update the monitor timestamp and attempt to acquire a lock to
        // check the wait times. We use Redis to do it in order to have the atomic
        // operation required. This will avoid any deadlocks or race conditions.
        $this->lastMonitored = CarbonImmutable::now();

        return $this->metrics->acquireWaitTimeMonitorLock();
    }

    /**
     * Determine if enough time has elapsed to attempt to monitor.
     *
     * @return bool
     */
    protected function timeToMonitor() {
        return CarbonImmutable::now()->subMinutes(1)->lte($this->lastMonitored);
    }
}
