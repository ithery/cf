<?php

class CTesting_Fake_Queue_PendingBatchFake extends CQueue_PendingBatch {
    /**
     * The fake bus instance.
     *
     * @var \CTesting_Fake_Base_BusFake
     */
    protected $bus;

    /**
     * Create a new pending batch instance.
     *
     * @param \CTesting_Fake_Base_BusFake $bus
     * @param \CCollection                $jobs
     *
     * @return void
     */
    public function __construct(CTesting_Fake_Base_BusFake $bus, CCollection $jobs) {
        $this->bus = $bus;
        $this->jobs = $jobs;
    }

    /**
     * Dispatch the batch.
     *
     * @return \CQueue_Batch
     */
    public function dispatch() {
        return $this->bus->recordPendingBatch($this);
    }
}
