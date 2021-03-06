<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 3:10:18 AM
 */
trait CQueue_Trait_InteractsWithQueue {
    /**
     * The underlying queue job instance.
     *
     * @var CQueue_AbstractJob
     */
    public $job;

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts() {
        return $this->job ? $this->job->attempts() : 1;
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete() {
        if ($this->job) {
            return $this->job->delete();
        }
    }

    /**
     * Fail the job from the queue.
     *
     * @param null|\Throwable $exception
     *
     * @return void
     */
    public function fail($exception = null) {
        if ($this->job) {
            $this->job->fail($exception);
        }
    }

    /**
     * Release the job back into the queue.
     *
     * @param int $delay
     *
     * @return void
     */
    public function release($delay = 0) {
        if ($this->job) {
            return $this->job->release($delay);
        }
    }

    /**
     * Set the base queue job instance.
     *
     * @param CQueue_AbstractJob $job
     *
     * @return $this
     */
    public function setJob(CQueue_AbstractJob $job) {
        $this->job = $job;

        return $this;
    }
}
