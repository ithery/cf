<?php
use DebugBar\DataCollector\MemoryCollector;

trait CDebug_DebugBar_DebugBarTrait_MemoryCollectorTrait {
    /**
     * Create and setup MemoryCollector.
     *
     * @return null|\DebugBar\DataCollector\MemoryCollector
     */
    public function setupMemoryCollector() {
        if ($this->shouldCollect('memory', true)) {
            /** @var CDebug_DebugBar $this */
            $memoryCollector = new MemoryCollector();

            $this->addCollector($memoryCollector);

            return $memoryCollector;
        }

        return null;
    }
}
