<?php

class CQueue_PendingClosureDispatch extends CQueue_PendingDispatch {
    /**
     * Add a callback to be executed if the job fails.
     *
     * @param \Closure $callback
     *
     * @return $this
     */
    public function catch($callback) {
        $this->job->onFailure($callback);

        return $this;
    }
}
