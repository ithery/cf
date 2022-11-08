<?php

class CQueue_Job_FileJob extends CQueue_AbstractJob {
    /**
     * The File queue instance.
     *
     * @var CQueue_Queue_FileQueue
     */
    protected $fileQueue;

    /**
     * The raw job payload.
     *
     * @var string
     */
    protected $job;

    /**
     * The JSON decoded version of "$job".
     *
     * @var array
     */
    protected $decoded;

    /**
     * Create a new job instance.
     *
     * @param CContainer             $container
     * @param CQueue_Queue_FileQueue $fileQueue
     * @param string                 $job
     * @param string                 $queue
     *
     * @return void
     */
    public function __construct(CContainer_Container $container, CQueue_Queue_FileQueue $fileQueue, $job, $queue) {
        $this->fileQueue = $fileQueue;
        $this->job = $job;
        $this->queue = $queue;
        $this->container = $container;
        $this->decoded = $this->payload();
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody() {
        return $this->job;
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete() {
        parent::delete();
        $this->fileQueue->popOrRelease($this->queue, $this->job, false);
    }

    /**
     * Release the job back into the queue.
     *
     * @param int $delay
     *
     * @return void
     */
    public function release($delay = 0) {
        parent::release($delay);
        $this->fileQueue->popOrRelease($this->queue, $this->job, false);
        $this->fileQueue->later($delay, $this, carr::get($this->decoded, 'data', ''), $this->queue);
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts() {
        return ($this->decoded['attempts'] ?? 0) + 1;
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId() {
        return $this->decoded['id'] ?? null;
    }
}
