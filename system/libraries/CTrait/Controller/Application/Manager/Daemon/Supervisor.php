<?php
trait CTrait_Controller_Application_Manager_Daemon_Supervisor {
    protected function bootSupervisor() {
        CView::blade()->component(\CDaemon_Supervisor_View_Component_StackTraceComponent::class, 'stack-trace');
    }

    protected function getTitle() {
        return 'Supervisor Manager';
    }

    public function index() {
        $app = c::app();

        $app->title($this->getTitle());

        $tabs = $app->addTabList()->setTabPositionLeft();
        $tabs->addTab()->setLabel('Metrics')->setAjaxUrl($this->controllerUrl() . 'tab/metrics')
            ->setNoPadding();
        $tabs->addTab()->setLabel('Failed Jobs')->setAjaxUrl($this->controllerUrl() . 'tab/failed')
            ->setNoPadding();

        $tabs->addTab()->setLabel('Dashboard')->setAjaxUrl($this->controllerUrl() . 'tab/dashboard')
            ->setNoPadding();

        $tabs->addTab()->setLabel('Monitoring')->setAjaxUrl($this->controllerUrl() . 'tab/monitoring')
            ->setNoPadding();

        $tabs->addTab()->setLabel('Batches')->setAjaxUrl($this->controllerUrl() . 'tab/batches')
            ->setNoPadding();

        $tabs->addTab()->setLabel('Jobs')->setAjaxUrl($this->controllerUrl() . 'tab/jobs')
            ->setNoPadding();

        CManager::registerModule('moment');

        return $app;
    }

    public function tab($method, $submethod = null) {
        if ($method == 'dashboard') {
            return $this->tabDashboard();
        }
        if ($method == 'monitoring') {
            return $this->tabMonitoring();
        }
        if ($method == 'jobs') {
            return $this->tabJobs($submethod);
        }
        if ($method == 'failed') {
            return $this->tabFailed();
        }
        if ($method == 'batches') {
            return $this->tabBatches();
        }
        if ($method == 'metrics') {
            return $this->tabMetrics($submethod);
        }
    }

    public function ajax($method, $submethod = null) {
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

        if ($method == 'jobs') {
            return $this->ajaxJobs($submethod);
        }
        if ($method == 'failed') {
            return $this->ajaxFailed($submethod);
        }
        if ($method == 'batches') {
            return $this->ajaxBatches();
        }
        if ($method == 'metrics') {
            return $this->ajaxMetrics($submethod);
        }
    }

    public function modal($method, $submethod = null) {
        if ($method == 'jobs') {
            return $this->modalJobs($submethod);
        }
        if ($method == 'failed') {
            return $this->modalFailed($submethod);
        }
        if ($method == 'metrics') {
            return $this->modalMetrics($submethod);
        }
    }

    protected function modalJobs($type) {
        $app = c::app();
        $jobId = c::request()->jobId;
        $job = (array) CDaemon::supervisor()->jobRepository()->getJobs([$jobId])->map(function ($job) {
            return $this->decodeJob($job);
        })->first();

        $app->addView('cresenity.daemon.supervisor.modal.modal-jobs', [
            'job' => $job,
        ]);

        return $app;
    }

    protected function modalFailed() {
        $app = c::app();
        $jobId = c::request()->jobId;
        $job = (array) CDaemon::supervisor()->jobRepository()->getJobs([$jobId])->map(function ($job) {
            return $this->decodeFailedJob($job);
        })->first();

        $ajaxFailedDetailUrl = $this->controllerUrl() . 'ajax/failed/detail';
        $ajaxFailedRetryUrl = $this->controllerUrl() . 'ajax/failed/retry';
        $modalFailedUrl = $this->controllerUrl() . 'modal/failed';
        $app->addView('cresenity.daemon.supervisor.modal.modal-failed', [
            'ajaxFailedDetailUrl' => $ajaxFailedDetailUrl,
            'ajaxFailedRetryUrl' => $ajaxFailedRetryUrl,
            'modalFailedUrl' => $modalFailedUrl,
            'job' => $job,
        ]);

        return $app;
    }

    protected function modalMetrics($type) {
        $app = c::app();
        $slug = c::request()->slug;

        $ajaxMetricsUrl = $this->controllerUrl() . 'ajax/metrics/' . $type;
        $app->addView('cresenity.daemon.supervisor.modal.modal-metrics', [
            'ajaxMetricsUrl' => $ajaxMetricsUrl,
            'type' => $type,
            'slug' => $slug,
        ]);

        return $app;
    }

    protected function ajaxFailedRetry() {
        $jobId = c::request()->jobId;
        c::dispatch(new CDaemon_Supervisor_TaskQueue_RetryFailedJob($jobId));

        return c::response()->json([
            'errCode' => 0,
            'errMessage' => '',
            'data' => []
        ]);
    }

    protected function ajaxFailedDetail() {
        $jobId = c::request()->jobId;
        $data = (array) CDaemon::supervisor()->jobRepository()->getJobs([$jobId])->map(function ($job) {
            return $this->decodeFailedJob($job);
        })->first();

        return c::response()->json([
            'errCode' => 0,
            'errMessage' => '',
            'data' => $data
        ]);
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

    protected function ajaxJobs($type) {
        $data = [];
        $request = c::request();
        if ($type == 'pending') {
            $jobs = CDaemon::supervisor()->jobRepository()->getPending($request->query('starting_at', -1))->map(function ($job) {
                $job->payload = json_decode($job->payload);

                return $job;
            })->values();

            $data = [
                'jobs' => $jobs,
                'total' => CDaemon::supervisor()->jobRepository()->countPending(),
            ];
        }

        if ($type == 'completed') {
            $jobs = CDaemon::supervisor()->jobRepository()->getCompleted($request->query('starting_at', -1))->map(function ($job) {
                $job->payload = json_decode($job->payload);

                return $job;
            })->values();

            $data = [
                'jobs' => $jobs,
                'total' => CDaemon::supervisor()->jobRepository()->countCompleted(),
            ];
        }

        if ($type == 'silenced') {
            $jobs = CDaemon::supervisor()->jobRepository()->getSilenced($request->query('starting_at', -1))->map(function ($job) {
                $job->payload = json_decode($job->payload);

                return $job;
            })->values();

            $data = [
                'jobs' => $jobs,
                'total' => CDaemon::supervisor()->jobRepository()->countSilenced(),
            ];
        }

        return c::response()->json([
            'errCode' => 0,
            'errMessage' => '',
            'data' => $data,
        ]);
    }

    protected function ajaxFailed($submethod = null) {
        if ($submethod == 'retry') {
            return $this->ajaxFailedRetry();
        }
        if ($submethod == 'detail') {
            return $this->ajaxFailedDetail();
        }
        $data = [];
        $request = c::request();
        $jobs = [];
        if ($tag = $request->query('tag')) {
            $jobIds = CDaemon::supervisor()->tagRepository()->paginate(
                'failed:' . $tag,
                ($request->query('starting_at') ?: -1) + 1,
                50
            );

            $startingAt = $request->query('starting_at', 0);

            $jobs = CDaemon::supervisor()->jobRepository()->getJobs($jobIds, $startingAt)->map(function ($job) {
                return $this->decodeFailedJob($job);
            });
        } else {
            $jobs = CDaemon::supervisor()->jobRepository()->getFailed($request->query('starting_at') ?: -1)->map(function ($job) {
                return $this->decodeFailedJob($job);
            });
        }
        $total = $request->query('tag')
            ? CDaemon::supervisor()->tagRepository()->count('failed:' . $request->query('tag'))
            : CDaemon::supervisor()->jobRepository()->countFailed();
        $data = [
            'jobs' => $jobs,
            'total' => $total,
        ];

        return c::response()->json([
            'errCode' => 0,
            'errMessage' => '',
            'data' => $data,
        ]);
    }

    protected function ajaxBatches() {
        $data = [];
        $request = c::request();
        $batches = [];

        try {
            $batches = CQueue::batchRepository()->get(50, $request->query('before_id') ?: null);
        } catch (CDatabase_Exception_QueryException $e) {
            $batches = [];
        }

        $data = [
            'batches' => $batches,
        ];

        return c::response()->json([
            'errCode' => 0,
            'errMessage' => '',
            'data' => $data,
        ]);
    }

    protected function ajaxMetrics($type) {
        $data = [];
        $request = c::request();

        if ($type == 'job') {
            $data = c::collect(CDaemon::supervisor()->metricsRepository()->snapshotsForJob($request->slug))->map(function ($record) {
                $record->runtime = round($record->runtime / 1000, 3);
                $record->throughput = (int) $record->throughput;

                return $record;
            });
        }

        if ($type == 'queue') {
            $data = c::collect(CDaemon::supervisor()->metricsRepository()->snapshotsForQueue($request->slug))->map(function ($record) {
                $record->runtime = round($record->runtime / 1000, 3);
                $record->throughput = (int) $record->throughput;

                return $record;
            });
        }

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
            $master->supervisors = $supervisors->get($name, []);
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

    protected function tabJobsPending() {
        $app = c::app();
        $ajaxTagsUrl = $this->controllerUrl() . 'ajax/tags';
        $app->addView('cresenity.daemon.supervisor.monitoring', [
            'ajaxTagsUrl' => $ajaxTagsUrl,
        ]);

        return $app;
    }

    protected function tabJobs($type = null) {
        $app = c::app();

        if ($type != null) {
            $ajaxJobsUrl = $this->controllerUrl() . 'ajax/jobs/' . $type;
            $modalJobsUrl = $this->controllerUrl() . 'modal/jobs/' . $type;
            $app->addView('cresenity.daemon.supervisor.jobs', [
                'ajaxJobsUrl' => $ajaxJobsUrl,
                'modalJobsUrl' => $modalJobsUrl,
                'type' => $type,
            ]);

            return $app;
        }
        $tabs = $app->addTabList()->setTabPositionTop();
        $tabs->addTab()->setLabel('Pending')->setAjaxUrl($this->controllerUrl() . 'tab/jobs/pending')
            ->setNoPadding();
        $tabs->addTab()->setLabel('Completed')->setAjaxUrl($this->controllerUrl() . 'tab/jobs/completed')
            ->setNoPadding();
        $tabs->addTab()->setLabel('Silenced')->setAjaxUrl($this->controllerUrl() . 'tab/jobs/silenced')
            ->setNoPadding();

        return $app;
    }

    protected function tabFailed() {
        $app = c::app();

        $ajaxFailedUrl = $this->controllerUrl() . 'ajax/failed';
        $ajaxFailedRetryUrl = $this->controllerUrl() . 'ajax/failed/retry';
        $modalFailedUrl = $this->controllerUrl() . 'modal/failed';
        $app->addView('cresenity.daemon.supervisor.failed', [
            'ajaxFailedUrl' => $ajaxFailedUrl,
            'ajaxFailedRetryUrl' => $ajaxFailedRetryUrl,
            'modalFailedUrl' => $modalFailedUrl,
        ]);

        return $app;
    }

    protected function tabBatches() {
        $app = c::app();

        $ajaxBatchesUrl = $this->controllerUrl() . 'ajax/batches';
        $app->addView('cresenity.daemon.supervisor.batches', [
            'ajaxBatchesUrl' => $ajaxBatchesUrl,
        ]);

        return $app;
    }

    protected function tabMetrics($type) {
        $app = c::app();

        if ($type == 'job') {
            $jobs = CDaemon::supervisor()->metricsRepository()->measuredJobs();

            $modalMetricJobUrl = $this->controllerUrl() . 'modal/metrics/job';
            $app->addView('cresenity.daemon.supervisor.metrics.job', [
                'modalMetricJobUrl' => $modalMetricJobUrl,
                'jobs' => $jobs,
            ]);

            return $app;
        }
        if ($type == 'queue') {
            $queues = CDaemon::supervisor()->metricsRepository()->measuredQueues();

            $modalMetricQueueUrl = $this->controllerUrl() . 'modal/metrics/queue';
            $app->addView('cresenity.daemon.supervisor.metrics.queue', [
                'modalMetricQueueUrl' => $modalMetricQueueUrl,
                'queues' => $queues,
            ]);

            return $app;
        }
        $tabs = $app->addTabList()->setTabPositionTop();
        $tabs->addTab()->setLabel('Job')->setAjaxUrl($this->controllerUrl() . 'tab/metrics/job')
            ->setNoPadding();
        $tabs->addTab()->setLabel('Queues')->setAjaxUrl($this->controllerUrl() . 'tab/metrics/queue')
            ->setNoPadding();

        return $app;
    }

    private function decodeFailedJob($job) {
        $job->payload = json_decode($job->payload);

        $job->exception = mb_convert_encoding($job->exception, 'UTF-8');

        $job->context = json_decode($job->context);

        $job->retried_by = c::collect(!is_null($job->retried_by) ? json_decode($job->retried_by) : [])
            ->sortByDesc('retried_at')->values();

        return $job;
    }

    /**
     * Decode the given job.
     *
     * @param object $job
     *
     * @return object
     */
    protected function decodeJob($job) {
        $job->payload = json_decode($job->payload);

        return $job;
    }
}
