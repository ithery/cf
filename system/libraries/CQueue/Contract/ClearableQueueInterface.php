<?php

interface CQueue_Contract_ClearableQueueInterface {
    /**
     * Delete all of the jobs from the queue.
     *
     * @param string $queue
     *
     * @return int
     */
    public function clear($queue);
}
