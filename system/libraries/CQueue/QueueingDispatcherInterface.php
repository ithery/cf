<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CQueue_QueueingDispatcherInterface extends CQueue_DispatcherInterface {
    /**
     * Dispatch a command to its appropriate handler behind a queue.
     *
     * @param mixed $command
     *
     * @return mixed
     */
    public function dispatchToQueue($command);
}
