<?php

class CDaemon_Supervisor_Listener_UpdateJobMetrics {
    /**
     * Stop gathering metrics for a job.
     *
     * @param \CDaemon_Supervisor_Event_RedisEvent_JobDeleted $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_RedisEvent_JobDeleted $event) {
        if ($event->job->hasFailed()) {
            return;
        }

        $time = CDaemon_Supervisor::stopwatch()->check($id = $event->payload->id());

        CDaemon_Supervisor::metricsRepository()->incrementQueue(
            $event->job->getQueue(),
            $time
        );

        CDaemon_Supervisor::metricsRepository()->incrementJob(
            $event->payload->displayName(),
            $time
        );

        CDaemon_Supervisor::stopwatch()->forget($id);
    }
}
