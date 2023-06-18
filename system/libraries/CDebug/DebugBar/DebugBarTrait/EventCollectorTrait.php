<?php

trait CDebug_DebugBar_DebugBarTrait_EventCollectorTrait {
    /**
     * Create and setup EventCollector.
     *
     * @return null|CDebug_DebugBar_DataCollector_EventCollector
     */
    public function setupEventCollector() {
        /** @var CDebug_DebugBar $this */
        if ($this->shouldCollect('event', true)) {
            $startTime = c::request()->server('REQUEST_TIME_FLOAT');
            $eventCollector = new CDebug_DebugBar_DataCollector_EventCollector($startTime);
            $this->addCollector($eventCollector);
            CEvent::dispatcher()->subscribe($eventCollector);

            return $eventCollector;
        }

        return null;
    }
}
