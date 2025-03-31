<?php

trait CTrait_DispatchesJobs {
    /**
     * Dispatch a job to its appropriate handler.
     *
     * @param mixed $job
     *
     * @return mixed
     */
    protected function dispatch($job) {
        return c::dispatch($job);
    }

    /**
     * Dispatch a job to its appropriate handler in the current process.
     *
     * Queueable jobs will be dispatched to the "sync" queue.
     *
     * @param mixed $job
     *
     * @return mixed
     */
    public function dispatchSync($job) {
        return c::dispatchSync($job);
    }
}
