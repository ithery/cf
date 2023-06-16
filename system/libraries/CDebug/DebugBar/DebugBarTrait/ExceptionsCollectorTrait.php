<?php
use DebugBar\DataCollector\ExceptionsCollector;

trait CDebug_DebugBar_DebugBarTrait_ExceptionsCollectorTrait {
    /**
     * Create and setup MemoryCollector.
     *
     * @return null|\DebugBar\DataCollector\ExceptionsCollector
     */
    public function setupExceptionsCollector() {
        if ($this->shouldCollect('exceptions', true)) {
            /** @var CDebug_DebugBar $this */
            $exceptionsCollector = new ExceptionsCollector();

            $this->addCollector($exceptionsCollector);

            return $exceptionsCollector;
        }

        return null;
    }
}
