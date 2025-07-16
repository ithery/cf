<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CQueue_QueueingDispatcherInterface extends CQueue_DispatcherInterface {
    /**
     * Attempt to find the batch with the given ID.
     *
     * @param string $batchId
     *
     * @return null|\CQueue_Batch
     */
    public function findBatch(string $batchId);

    /**
     * Create a new batch of queueable jobs.
     *
     * @param \CCollection|array $jobs
     *
     * @return \CQueue_PendingBatch
     */
    public function batch($jobs);

    /**
     * Dispatch a command to its appropriate handler behind a queue.
     *
     * @param mixed $command
     *
     * @return mixed
     */
    public function dispatchToQueue($command);
}
