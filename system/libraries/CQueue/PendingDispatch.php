<?php

defined('SYSPATH') or die('No direct access allowed.');

class CQueue_PendingDispatch {
    /**
     * The job.
     *
     * @var mixed
     */
    protected $job;

    /**
     * Indicates if the job should be dispatched immediately after sending the response.
     *
     * @var bool
     */
    protected $afterResponse = false;

    /**
     * Create a new pending job dispatch.
     *
     * @param mixed $job
     *
     * @return void
     */
    public function __construct($job) {
        $this->job = $job;
    }

    /**
     * Set the desired connection for the job.
     *
     * @param null|string $connection
     *
     * @return $this
     */
    public function onConnection($connection) {
        $this->job->onConnection($connection);

        return $this;
    }

    /**
     * Set the desired queue for the job.
     *
     * @param null|string $queue
     *
     * @return $this
     */
    public function onQueue($queue) {
        $this->job->onQueue($queue);

        return $this;
    }

    /**
     * Set the desired connection for the chain.
     *
     * @param null|string $connection
     *
     * @return $this
     */
    public function allOnConnection($connection) {
        $this->job->allOnConnection($connection);

        return $this;
    }

    /**
     * Set the desired queue for the chain.
     *
     * @param null|string $queue
     *
     * @return $this
     */
    public function allOnQueue($queue) {
        $this->job->allOnQueue($queue);

        return $this;
    }

    /**
     * Set the desired delay for the job.
     *
     * @param null|\DateTimeInterface|\DateInterval|int $delay
     *
     * @return $this
     */
    public function delay($delay) {
        $this->job->delay($delay);

        return $this;
    }

    /**
     * Indicate that the job should be dispatched after all database transactions have committed.
     *
     * @return $this
     */
    public function afterCommit() {
        $this->job->afterCommit();

        return $this;
    }

    /**
     * Indicate that the job should not wait until database transactions have been committed before dispatching.
     *
     * @return $this
     */
    public function beforeCommit() {
        $this->job->beforeCommit();

        return $this;
    }

    /**
     * Set the jobs that should run if this job is successful.
     *
     * @param array $chain
     *
     * @return $this
     */
    public function chain($chain) {
        $this->job->chain($chain);

        return $this;
    }

    /**
     * Indicate that the job should be dispatched after the response is sent to the browser.
     *
     * @return $this
     */
    public function afterResponse() {
        $this->afterResponse = true;

        return $this;
    }

    /**
     * Determine if the job should be dispatched.
     *
     * @return bool
     */
    protected function shouldDispatch() {
        if (!$this->job instanceof CQueue_Contract_ShouldBeUniqueInterface) {
            return true;
        }

        return (new CQueue_UniqueLock(c::cache()->driver()))
            ->acquire($this->job);
    }

    /**
     * Dynamically proxy methods to the underlying job.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return $this
     */
    public function __call($method, $parameters) {
        $this->job->{$method}(...$parameters);

        return $this;
    }

    /**
     * Handle the object's destruction.
     *
     * @return void
     */
    public function __destruct() {
        if (!$this->shouldDispatch()) {
            return;
        } elseif ($this->afterResponse) {
            CQueue::dispatcher()->dispatchAfterResponse($this->job);
        } else {
            CQueue::dispatcher()->dispatch($this->job);
        }
    }
}
