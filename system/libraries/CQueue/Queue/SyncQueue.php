<?php
class CQueue_Queue_SyncQueue extends CQueue_AbstractQueue {
    /**
     * Get the size of the queue.
     *
     * @param string|null $queue
     *
     * @return int
     */
    public function size($queue = null) {
        return 0;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param string      $job
     * @param mixed       $data
     * @param string|null $queue
     *
     * @return mixed
     *
     * @throws \Throwable
     */
    public function push($job, $data = '', $queue = null) {
        $queueJob = $this->resolveJob($this->createPayload($job, $queue, $data), $queue);

        try {
            $this->raiseBeforeJobEvent($queueJob);

            $queueJob->fire();

            $this->raiseAfterJobEvent($queueJob);
        } catch (Throwable $e) {
            $this->handleException($queueJob, $e);
        }

        return 0;
    }

    /**
     * Resolve a Sync job instance.
     *
     * @param string $payload
     * @param string $queue
     *
     * @return \CQueue_Job_SyncJob
     */
    protected function resolveJob($payload, $queue) {
        return new CQueue_Job_SyncJob($this->container, $payload, $this->connectionName, $queue);
    }

    /**
     * Raise the before queue job event.
     *
     * @param \CQueue_JobInterface $job
     *
     * @return void
     */
    protected function raiseBeforeJobEvent(CQueue_JobInterface $job) {
        if ($this->container->bound('events')) {
            $this->container['events']->dispatch(new CQueue_Event_JobProcessing($this->connectionName, $job));
        }
    }

    /**
     * Raise the after queue job event.
     *
     * @param \CQueue_JobInterface $job
     *
     * @return void
     */
    protected function raiseAfterJobEvent(CQueue_JobInterface $job) {
        if ($this->container->bound('events')) {
            $this->container['events']->dispatch(new CQueue_Event_JobProcessed($this->connectionName, $job));
        }
    }

    /**
     * Raise the exception occurred queue job event.
     *
     * @param \Illuminate\Contracts\Queue\Job $job
     * @param \Throwable                      $e
     *
     * @return void
     */
    protected function raiseExceptionOccurredJobEvent(CQueue_JobInterface $job, Throwable $e) {
        if ($this->container->bound('events')) {
            $this->container['events']->dispatch(new CQueue_Event_JobExceptionOccurred($this->connectionName, $job, $e));
        }
    }

    /**
     * Handle an exception that occurred while processing a job.
     *
     * @param \Illuminate\Contracts\Queue\Job $queueJob
     * @param \Throwable                      $e
     *
     * @return void
     *
     * @throws \Throwable
     */
    protected function handleException(CQueue_JobInterface $queueJob, Throwable $e) {
        $this->raiseExceptionOccurredJobEvent($queueJob, $e);

        $queueJob->fail($e);

        throw $e;
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param string      $payload
     * @param string|null $queue
     * @param array       $options
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = []) {
        //
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTimeInterface|\DateInterval|int $delay
     * @param string                               $job
     * @param mixed                                $data
     * @param string|null                          $queue
     *
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null) {
        return $this->push($job, $data, $queue);
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string|null $queue
     *
     * @return \CQueue_JobInterface|null
     */
    public function pop($queue = null) {
        //
    }
}