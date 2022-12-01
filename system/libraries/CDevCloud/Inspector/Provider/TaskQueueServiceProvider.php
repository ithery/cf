<?php

use Inspector\Models\Segment;

class CDevCloud_Inspector_Provider_TaskQueueServiceProvider extends CDevCloud_Inspector_ProviderAbstract {
    /**
     * Jobs to inspect.
     *
     * @var Segment[]
     */
    protected $segments = [];

    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot() {
        CEvent::dispatcher()->listen(
            CQueue_Event_JobProcessing::class,
            function (CQueue_Event_JobProcessing $event) {
                $this->handleJobStart($event->job);
            }
        );

        CEvent::dispatcher()->listen(
            CQueue_Event_JobProcessed::class,
            function (CQueue_Event_JobProcessed $event) {
                $this->handleJobEnd($event->job);
            }
        );

        CEvent::dispatcher()->listen(
            CQueue_Event_JobFailed::class,
            function (CQueue_Event_JobFailed $event) {
                $this->handleJobEnd($event->job, true);
            }
        );

        CEvent::dispatcher()->listen(
            CQueue_Event_JobExceptionOccurred::class,
            function (CQueue_Event_JobExceptionOccurred $event) {
                $this->handleJobEnd($event->job, true);
            }
        );
    }

    /**
     * Determine the way to monitor the job.
     *
     * @param CQueue_AbstractJob $job
     */
    protected function handleJobStart(CQueue_AbstractJob $job) {
        // Ignore job.
        if (!$this->shouldBeMonitored($job->resolveName())) {
            return;
        }

        if (CDevCloud::inspector()->needTransaction()) {
            CDevCloud::inspector()->startTransaction($job->resolveName())
                ->addContext('Payload', $job->payload());
        } elseif (CDevCloud::inspector()->canAddSegments()) {
            $this->initializeSegment($job);
        }
    }

    /**
     * Representing a job as a segment.
     *
     * @param CQueue_AbstractJob $job
     */
    protected function initializeSegment(CQueue_AbstractJob $job) {
        $segment = CDevCloud::inspector()->startSegment('job', $job->resolveName())
            ->addContext('Payload', $job->payload());

        // Save the job under a unique ID
        $this->segments[$this->getJobId($job)] = $segment;
    }

    /**
     * Finalize the monitoring of the job.
     *
     * @param CQueue_AbstractJob $job
     * @param bool               $failed
     */
    public function handleJobEnd(CQueue_AbstractJob $job, $failed = false) {
        if (!$this->shouldBeMonitored($job->resolveName())) {
            return;
        }

        $id = $this->getJobId($job);

        if (array_key_exists($id, $this->segments)) {
            $this->segments[$id]->end();
        } else {
            CDevCloud::inspector()->currentTransaction()
                ->setResult($failed ? 'error' : 'success');
        }

        // Flush normally happens at shutdown... which only happens in the worker if it is run in a standalone execution.
        // Flush immediately if the job is running in a background worker.
        if ($this->app->runningInConsole()) {
            CDevCloud::inspector()->flush();
        }
    }

    /**
     * Get the job ID.
     *
     * @param CQueue_AbstractJob $job
     *
     * @return string|int
     */
    public static function getJobId(CQueue_AbstractJob $job) {
        if ($jobId = $job->getJobId()) {
            return $jobId;
        }

        return sha1($job->getRawBody());
    }

    /**
     * Determine if the given job needs to be monitored.
     *
     * @param string $job
     *
     * @return bool
     */
    protected function shouldBeMonitored(string $job): bool {
        return CDevCloud_Inspector_Filters::isApprovedJobClass($job, CF::config('devcloud.inspector.ignore_jobs')) && CDevCloud::inspector()->isRecording();
    }
}
