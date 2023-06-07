<?php

trait CQueue_Trait_BatchableTrait {
    /**
     * The batch ID (if applicable).
     *
     * @var string
     */
    public $batchId;

    /**
     * Get the batch instance for the job, if applicable.
     *
     * @return null|\CQueue_Batch
     */
    public function batch() {
        if ($this->batchId) {
            return CQueue::batchRepository()->find($this->batchId);
        }
    }

    /**
     * Determine if the batch is still active and processing.
     *
     * @return bool
     */
    public function batching() {
        $batch = $this->batch();

        return $batch && !$batch->cancelled();
    }

    /**
     * Set the batch ID on the job.
     *
     * @param string $batchId
     *
     * @return $this
     */
    public function withBatchId($batchId) {
        $this->batchId = $batchId;

        return $this;
    }
}
