<?php

class CQueue_Event_BatchDispatched {
    /**
     * The batch instance.
     *
     * @var \CQueue_Batch
     */
    public $batch;

    /**
     * Create a new event instance.
     *
     * @param \CQueue_Batch $batch
     *
     * @return void
     */
    public function __construct(CQueue_Batch $batch) {
        $this->batch = $batch;
    }
}
