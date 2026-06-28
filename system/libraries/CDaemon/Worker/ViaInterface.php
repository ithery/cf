<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CDaemon_Worker_ViaInterface {
    /**
     * Validate any dependencies.
     *
     * @param array $errors
     *
     * @return array
     */
    public function checkEnvironment(array $errors = []);

    /**
     * Puts the message on the queue.
     *
     * @param CDaemon_Worker_Call $message
     *
     * @return bool
     */
    public function put(CDaemon_Worker_Call $message);

    /**
     * Retrieves a message from the queue.
     *
     * @param $desired_type
     * @param $blocking
     *
     * @return CDaemon_Worker_Call
     */
    public function get($desired_type, $blocking = false);

    /**
     * Handle an Error.
     *
     * @param mixed $error
     * @param mixed $try
     *
     * @return mixed
     */
    public function error($error, $try = 1);

    /**
     * The state of the queue -- The number of pending messages, memory consumption, errors, etc.
     *
     * @return array with some subset of these keys: messages, memory_allocation, error_count
     */
    public function state();

    /**
     * Drop the single message.
     *
     * @param mixed $callId
     *
     * @return void
     */
    public function drop($callId);

    /**
     * Drop any pending messages in the queue.
     *
     * @return void
     */
    public function purge();

    /**
     * Remove and release any resources consumed by this Via. For SysV, this means removing the SHM and MQ resources.
     * In other cases, a blank implementation would also be fine: We don't want to drop a logical queue in RabbitMQ for example just because we're shutting down the listener.
     *
     * @return mixed
     */
    public function release();
}
