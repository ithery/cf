<?php
use DebugBar\DataCollector\TimeDataCollector;

trait CDebug_DebugBar_DebugBarTrait_TimeDataCollectorTrait {
    /**
     * Create and setup TimeDataCollector.
     *
     * @return null|\DebugBar\DataCollector\TimeDataCollector
     */
    public function setupTimeDataCollector() {
        /** @var CDebug_DebugBar $this */
        if ($this->shouldCollect('time', true)) {
            $startTime = c::request()->server('REQUEST_TIME_FLOAT');
            $timeDataCollector = new TimeDataCollector($startTime);
            $debugbar = $this;
            CF::booted(function () use ($debugbar, $startTime) {
                $debugbar['time']->addMeasure('Booting', $startTime, microtime(true));
            });
            CFBenchmark::onStopCallback(function ($name, $data) use ($timeDataCollector) {
                $timeDataCollector->addMeasure($name, carr::get($data, 'start'), carr::get($data, 'stop'));
            });
            $completedBenchmarks = CFBenchmark::completed();
            foreach ($completedBenchmarks as $name => $data) {
                $timeDataCollector->addMeasure($name, carr::get($data, 'start'), carr::get($data, 'stop'));
            }
            $this->addCollector($timeDataCollector);
            $this->startMeasure('application', 'Application');

            return $timeDataCollector;
        }

        return null;
    }
}
