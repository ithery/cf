<?php

defined('SYSPATH') or die('No direct access allowed.');

class CQueue_Exception_MaxAttemptsExceededException extends Exception {
    /**
     * The job that exceeded its attempts.
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
        $exception = new static($job->resolveName() . ' has been attempted too many times.');
        $exception->job = $job;

        return $exception;
    }
}
