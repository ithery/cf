<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CQueue_Contract_MonitorInterface {
    /**
     * Register a callback to be executed when a daemon queue is starting.
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function starting($callback);

    /**
     * Register a callback to be executed on every iteration through the queue loop.
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function looping($callback);

    /**
     * Register a callback to be executed when a job fails after the maximum amount of retries.
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function failing($callback);

    /**
     * Register a callback to be executed when a daemon queue is stopping.
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function stopping($callback);
}
