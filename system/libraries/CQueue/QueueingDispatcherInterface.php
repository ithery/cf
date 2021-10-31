<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 2:40:21 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CQueue_QueueingDispatcherInterface extends CQueue_DispatcherInterface {

    /**
     * Dispatch a command to its appropriate handler behind a queue.
     *
     * @param  mixed  $command
     * @return mixed
     */
    public function dispatchToQueue($command);
}
