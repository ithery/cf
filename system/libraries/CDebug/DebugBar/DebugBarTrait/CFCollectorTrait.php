<?php

trait CDebug_DebugBar_DebugBarTrait_CFCollectorTrait {
    /**
     * Create and setup CFCollector.
     *
     * @return null|CDebug_DebugBar_DataCollector_CFCollector
     */
    public function setupCFCollector() {
        if ($this->shouldCollect('cf', true)) {
            /** @var CDebug_DebugBar $this */
            $cfCollector = new CDebug_DebugBar_DataCollector_CFCollector();

            $this->addCollector($cfCollector);

            return $cfCollector;
        }

        return null;
    }
}
