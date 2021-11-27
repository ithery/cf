<?php

class CExporter_TaskQueue_AfterImportJob implements CQueue_ShouldQueueInterface {
    use CQueue_Trait_QueueableTrait;
    use CExporter_Trait_HasEventBusTrait;
    /**
     * @var CExporter_Concern_WithEvents
     */
    private $import;

    /**
     * @var CExporter_Reader
     */
    private $reader;

    /**
     * @param object           $import
     * @param CExporter_Reader $reader
     */
    public function __construct($import, CExporter_Reader $reader) {
        $this->import = $import;
        $this->reader = $reader;
    }

    public function handle() {
        if ($this->import instanceof CQueue_ShouldQueueInterface && $this->import instanceof CExporter_Concern_WithEvents) {
            $this->reader->clearListeners();
            $this->reader->registerListeners($this->import->registerEvents());
        }

        $this->reader->afterImport($this->import);
    }

    /**
     * @param Throwable $e
     */
    public function failed($e) {
        if ($this->import instanceof CExporter_Concern_WithEvents) {
            $this->registerListeners($this->import->registerEvents());
            $this->raise(new CExporter_Event_ImportFailed($e));

            if (method_exists($this->import, 'failed')) {
                $this->import->failed($e);
            }
        }
    }
}
