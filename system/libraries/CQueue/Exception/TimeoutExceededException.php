<?php

defined('SYSPATH') or die('No direct access allowed.');

class CQueue_Exception_TimeoutExceededException extends CQueue_Exception_MaxAttemptsExceededException {
    /**
     * The job that timed out.
     *
     * @var null|CQueue_JobInterface
     */
    public $job;

    /**
     * @param CQueue_JobInterface $job
     *
     * @return static
     */
    public static function forJob($job) {
        $exception = new static($job->resolveName() . ' has timed out.');
        $exception->job = $job;

        return $exception;
    }
}
