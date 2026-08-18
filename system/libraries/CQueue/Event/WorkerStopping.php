<?php

defined('SYSPATH') or die('No direct access allowed.');

class CQueue_Event_WorkerStopping {
    /**
     * The exit status.
     *
     * @var int
     */
    public $status;

    /**
     * The worker options.
     *
     * @var null|CQueue_WorkerOptions
     */
    public $options;

    /**
     * Why the worker stopped, one of the CQueue_WorkerStopReason constants.
     *
     * @var null|string
     */
    public $reason;

    /**
     * How many jobs this worker processed before stopping.
     *
     * @var null|int
     */
    public $jobsProcessed;

    /**
     * When the last job finished, as a high-resolution timestamp.
     *
     * @var null|float
     */
    public $lastJobProcessedAt;

    /**
     * Memory in use when the worker stopped, in MB.
     *
     * @var null|float
     */
    public $memoryUsage;

    /**
     * Create a new event instance.
     *
     * @param int                       $status
     * @param null|CQueue_WorkerOptions $options
     * @param null|string               $reason
     * @param null|int                  $jobsProcessed
     * @param null|float                $lastJobProcessedAt
     * @param null|float                $memoryUsage
     *
     * @return void
     */
    public function __construct($status = 0, $options = null, $reason = null, $jobsProcessed = null, $lastJobProcessedAt = null, $memoryUsage = null) {
        $this->status = $status;
        $this->options = $options;
        $this->reason = $reason;
        $this->jobsProcessed = $jobsProcessed;
        $this->lastJobProcessedAt = $lastJobProcessedAt;
        $this->memoryUsage = $memoryUsage;
    }
}
