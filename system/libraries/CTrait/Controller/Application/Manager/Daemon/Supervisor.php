<?php
trait CTrait_Controller_Application_Manager_Daemon_Supervisor {
    protected function getTitle() {
        return 'Supervisor Manager';
    }

    public function index() {
        $app = c::app();

        $app->title($this->getTitle());

        $tabs = $app->addTabList()->setTabPositionLeft();
        $tabs->addTab()->setLabel('Dashboard')->setAjaxUrl($this->controllerUrl() . 'tab/dashboard');
        $tabs->addTab()->setLabel('Monitoring')->setAjaxUrl($this->controllerUrl() . 'tab/monitoring');

        return $app;
    }

    public function tab($method) {
        if ($method == 'dashboard') {
            return $this->tabDashboard();
        }
        if ($method == 'monitoring') {
            return $this->tabMonitoring();
        }
    }

    public function ajax($method) {
        if ($method == 'stat') {
            return $this->ajaxStat();
        }
        if ($method == 'workload') {
            return $this->ajaxWorkload();
        }
        if ($method == 'workers') {
            return $this->ajaxWorkers();
        }
        if ($method == 'tags') {
            return $this->ajaxTags();
        }
    }

    protected function ajaxStat() {
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

        return c::response()->json([
            'errCode' => 0,
            'errMessage' => '',
            'data' => [
                'failedJobs' => $failedJobs,
                'jobsPerMinute' => $jobsPerMinute,
                'pausedMasters' => $pausedMasters,
                'periods' => $periods,
                'processes' => $processes,
                'queueWithMaxRuntime' => $queueWithMaxRuntime,
                'queueWithMaxThroughput' => $queueWithMaxThroughput,
                'recentJobs' => $recentJobs,
                'status' => $status,
                'wait' => $wait,

            ]
        ]);
    }

    protected function ajaxWorkload() {
        $data = c::collect(CDaemon::supervisor()->workloadRepository()->get())->sortBy('name')->values()->toArray();

        return c::response()->json([
            'errCode' => 0,
            'errMessage' => '',
            'data' => $data,
        ]);
    }

    protected function ajaxTags() {
        $data = c::collect(CDaemon::supervisor()->tagRepository()->monitoring())->map(function ($tag) {
            return [
                'tag' => $tag,
                'count' => $this->tags->count($tag) + $this->tags->count('failed:' . $tag),
            ];
        })->sortBy('tag')->values();

        return c::response()->json([
            'errCode' => 0,
            'errMessage' => '',
            'data' => $data,
        ]);
    }

    protected function ajaxWorkers() {
        $masters = c::collect(CDaemon::supervisor()->masterSupervisorRepository()->all())->keyBy('name')->sortBy('name');

        $supervisors = c::collect(CDaemon::supervisor()->supervisorRepository()->all())->sortBy('name')->groupBy('master');

        $data = $masters->each(function ($master, $name) use ($supervisors) {
            $master->supervisors = (array) $supervisors->get($name);
        });

        return c::response()->json([
            'errCode' => 0,
            'errMessage' => '',
            'data' => $data,
        ]);
    }

    protected function tabDashboard() {
        $app = c::app();

        $ajaxStatUrl = $this->controllerUrl() . 'ajax/stat';
        $ajaxWorkloadUrl = $this->controllerUrl() . 'ajax/workload';
        $ajaxWorkersUrl = $this->controllerUrl() . 'ajax/workers';
        $app->addView('cresenity.daemon.supervisor.dashboard', [
            'ajaxStatUrl' => $ajaxStatUrl,
            'ajaxWorkloadUrl' => $ajaxWorkloadUrl,
            'ajaxWorkersUrl' => $ajaxWorkersUrl,
        ]);

        return $app;
    }

    protected function tabMonitoring() {
        $app = c::app();
        $ajaxTagsUrl = $this->controllerUrl() . 'ajax/tags';
        $app->addView('cresenity.daemon.supervisor.monitoring', [
            'ajaxTagsUrl' => $ajaxTagsUrl,
        ]);

        return $app;
    }
}
