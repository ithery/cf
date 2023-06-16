<?php

trait CDebug_DebugBar_DebugBarTrait_PhpInfoCollectorTrait {
    /**
     * Create and setup PhpInfoCollector.
     *
     * @return CDebug_DebugBar_DataCollector_PhpInfoCollector
     */
    public function createAndSetupPhpInfoCollector() {
        $phpInfoCollector = new CDebug_DebugBar_DataCollector_PhpInfoCollector();

        return $phpInfoCollector;
    }
}
