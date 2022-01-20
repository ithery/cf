<?php

class CExporter_TaskQueue_QueueImport implements CQueue_ShouldQueueInterface {
    use CExporter_Trait_ExtendedQueueableTrait;
    use CQueue_Trait_DispatchableTrait;
    /**
     * @var int
     */
    public $tries;

    /**
     * @var int
     */
    public $timeout;

    /**
     * @param CQueue_ShouldQueueInterface $import
     */
    public function __construct(CQueue_ShouldQueueInterface $import = null) {
        if ($import) {
            $this->timeout = $import->timeout ?? null;
            $this->tries = $import->tries ?? null;
        }
    }

    public function handle() {
    }
}
