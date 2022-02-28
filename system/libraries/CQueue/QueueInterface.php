<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 3:25:01 AM
 */
interface CQueue_QueueInterface {
    /**
     * Get the size of the queue.
     *
     * @param null|string $queue
     *
     * @return int
     */
    public function size($queue = null);

    /**
     * Push a new job onto the queue.
     *
     * @param string|object $job
     * @param mixed         $data
     * @param null|string   $queue
     *
     * @return mixed
     */
    public function push($job, $data = '', $queue = null);

    /**
     * Push a new job onto the queue.
     *
     * @param string        $queue
     * @param string|object $job
     * @param mixed         $data
     *
     * @return mixed
     */
    public function pushOn($queue, $job, $data = '');

    /**
     * Push a raw payload onto the queue.
     *
     * @param string      $payload
     * @param null|string $queue
     * @param array       $options
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = []);

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTimeInterface|\DateInterval|int $delay
     * @param string|object                        $job
     * @param mixed                                $data
     * @param null|string                          $queue
     *
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null);

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param string                               $queue
     * @param \DateTimeInterface|\DateInterval|int $delay
     * @param string|object                        $job
     * @param mixed                                $data
     *
     * @return mixed
     */
    public function laterOn($queue, $delay, $job, $data = '');

    /**
     * Push an array of jobs onto the queue.
     *
     * @param array       $jobs
     * @param mixed       $data
     * @param null|string $queue
     *
     * @return mixed
     */
    public function bulk($jobs, $data = '', $queue = null);

    /**
     * Pop the next job off of the queue.
     *
     * @param string $queue
     *
     * @return null|\CQueue_JobInterface
     */
    public function pop($queue = null);

    /**
     * Get the connection name for the queue.
     *
     * @return string
     */
    public function getConnectionName();

    /**
     * Set the connection name for the queue.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setConnectionName($name);
}
