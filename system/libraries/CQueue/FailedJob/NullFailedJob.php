<?php

defined('SYSPATH') or die('No direct access allowed.');

class CQueue_FailedJob_NullFailedJob extends CQueue_AbstractFailedJob {
    /**
     * Log a failed job into storage.
     *
     * @param string     $connection
     * @param string     $queue
     * @param string     $payload
     * @param \Exception $exception
     *
     * @return null|int
     */
    public function log($connection, $queue, $payload, $exception) {
    }

    /**
     * Get a list of all of the failed jobs.
     *
     * @return array
     */
    public function all() {
        return [];
    }

    /**
     * Get a single failed job.
     *
     * @param mixed $id
     *
     * @return null|object
     */
    public function find($id) {
    }

    /**
     * Delete a single failed job from storage.
     *
     * @param mixed $id
     *
     * @return bool
     */
    public function forget($id) {
        return true;
    }

    /**
     * Flush all of the failed jobs from storage.
     *
     * @return void
     */
    public function flush() {
    }
}
