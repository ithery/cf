<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 4:04:43 AM
 */
interface CQueue_MonitorInterface {
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
