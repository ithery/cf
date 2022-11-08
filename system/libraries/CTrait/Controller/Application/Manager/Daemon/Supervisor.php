<?php
trait CTrait_Controller_Application_Manager_Daemon_Supervisor {
    protected function getTitle() {
        return 'Supervisor Manager';
    }

    public function index() {
        $app = c::app();

        $app->title($this->getTitle());

        try {
            $failedJobs = CDaemon::supervisor()->jobRepository()->countRecentlyFailed();
            $jobsPerMinute = CDaemon::supervisor()->metricsRepository()->jobsProcessedPerMinute();
            $pausedMasters = CDaemon::supervisor()->totalPausedMasters();
            $periods = [
                'failedJobs' => CF::config('daemon.supervisor.trim.recent_failed', CF::config('daemon.supervisor.trim.failed')),
                'recentJobs' => CF::config('daemon.supervisor.trim.recent'),
            ];

            $processes = CDaemon::supervisor()->totalProcessCount();
            $queueWithMaxRuntime = CDaemon::supervisor()->metricsRepository()->queueWithMaximumRuntime();
            $queueWithMaxThroughput = CDaemon::supervisor()->metricsRepository()->queueWithMaximumThroughput();
            $recentJobs = CDaemon::supervisor()->jobRepository()->countRecent();
            $status = CDaemon::supervisor()->currentStatus();
            $wait = c::collect(CDaemon::supervisor()->waitTimeCalculator()->calculate())->take(1);
            $app->add('failedJobs:' . $failedJobs);
            $app->addBr();
            $app->add('jobsPerMinute:' . $jobsPerMinute);
            $app->addBr();
            $app->add('pausedMasters:' . $pausedMasters);
            $app->addBr();
            $app->add('periods.failedJobs:' . carr::get($periods, 'failedJobs'));
            $app->addBr();
            $app->add('periods.recentJobs:' . carr::get($periods, 'recentJobs'));
            $app->addBr();
            $app->add('processes:' . $processes);
            $app->addBr();
            $app->add('queueWithMaxRuntime:' . $queueWithMaxRuntime);
            $app->addBr();
            $app->add('queueWithMaxThroughput:' . $queueWithMaxThroughput);
            $app->addBr();
            $app->add('recentJobs:' . $recentJobs);
            $app->addBr();
            $app->add('status:' . $status);
            $app->addBr();
            $app->add('wait:' . json_encode($wait));
            $app->addBr();
        } catch(Exception $ex) {
            $app->addAlert()->setType('error')->add($ex->getMessage());
        }

        return $app;
    }
}
