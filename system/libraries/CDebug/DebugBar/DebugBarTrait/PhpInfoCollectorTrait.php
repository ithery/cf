<?php

trait CDebug_DebugBar_DebugBarTrait_PhpInfoCollectorTrait {
    /**
     * Create and setup PhpInfoCollector.
     *
     * @return null|CDebug_DebugBar_DataCollector_PhpInfoCollector
     */
    public function setupPhpInfoCollector() {
        /** @var CDebug_DebugBar $this */
        if ($this->shouldCollect('phpinfo', true)) {
            $phpInfoCollector = new CDebug_DebugBar_DataCollector_PhpInfoCollector();
            $this->addCollector($phpInfoCollector);

            return $phpInfoCollector;
        }

        return null;
    }
}
