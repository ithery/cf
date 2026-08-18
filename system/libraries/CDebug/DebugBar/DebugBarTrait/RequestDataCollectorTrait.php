<?php
use DebugBar\DataCollector\RequestDataCollector;

trait CDebug_DebugBar_DebugBarTrait_RequestDataCollectorTrait {
    /**
     * Create and setup MemoryCollector.
     *
     * @return null|\DebugBar\DataCollector\RequestDataCollector
     */
    public function setupRequestDataCollector() {
        if ($this->shouldCollect('request', true)) {
            /** @var CDebug_DebugBar $this */
            $requestDataCollector = new RequestDataCollector();

            $this->addCollector($requestDataCollector);

            return $requestDataCollector;
        }

        return null;
    }
}
