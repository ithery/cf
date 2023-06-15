<?php

class CDaemon_Supervisor_TaskQueue_SupervisorSnapshot {
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $lock = CDaemon::supervisor()->lock();
        $metrics = CDaemon::supervisor()->metricsRepository();
        if ($lock->get('metrics:snapshot', CF::config('daemon.supervisor.metrics.snapshot_lock', 300) - 30)) {
            $metrics->snapshot();
        }
    }
}
