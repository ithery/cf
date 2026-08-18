<?php

trait CDebug_DebugBar_DebugBarTrait_FilesCollectorTrait {
    /**
     * Create and setup FilesCollector.
     *
     * @return null|CDebug_DebugBar_DataCollector_FilesCollector
     */
    public function setupFilesCollector() {
        /** @var CDebug_DebugBar $this */
        if ($this->shouldCollect('files', true)) {
            $filesCollector = new CDebug_DebugBar_DataCollector_FilesCollector();
            $this->addCollector($filesCollector);

            return $filesCollector;
        }

        return null;
    }
}
