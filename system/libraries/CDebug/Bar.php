<?php

use DebugBar\DataCollector\TimeDataCollector;

class CDebug_Bar extends CDebug_AbstractBar {
    /**
     * True when booted.
     *
     * @var bool
     */
    protected $booted;

    /**
     * True when enabled, false disabled an null for still unknown.
     *
     * @var bool
     */
    protected $enabled;

    public function __construct($options = []) {
        parent::__construct($options);
    }

    public function getJavascriptCode() {
        return $this->renderer->getJavascriptCode();
    }

    public function getJavascriptReplaceCode() {
        return $this->renderer->getJavascriptReplaceCode();
    }

    public function isEnabled() {
        return $this->enabled;
    }

    public function disable() {
        $this->enabled = false;
    }

    public function enable() {
        $this->enabled = true;
        if (!$this->booted) {
            $this->boot();
        }
    }

    public function shouldCollect($name, $default = false) {
        return CF::config('debug.debugbar.collectors.' . $name, $default);
    }

    /**
     * Boot the debugbar (add collectors, renderer and listener).
     */
    public function boot() {
        if ($this->booted) {
            return;
        }

        $timeDataCollector = new TimeDataCollector();

        $this->addCollector(new CDebug_DataCollector_PhpInfoCollector());
        $this->addCollector(new CDebug_DataCollector_MemoryCollector());
        $this->addCollector(new CDebug_DataCollector_LocalizationCollector());
        $this->addCollector(new CDebug_DataCollector_MessagesCollector());
        $this->addCollector(new CDebug_DataCollector_EventCollector());
        $this->addCollector(new CDebug_DataCollector_RequestDataCollector());
        $this->addCollector($timeDataCollector);
        $this->addCollector(new CDebug_DataCollector_FilesCollector());
        $this->addCollector(new CDebug_DataCollector_RenderableCollector());

        $queryCollector = new CDebug_DataCollector_QueryCollector($timeDataCollector);

        $queryCollector->setRenderSqlWithParams(true);
        $this->addCollector($queryCollector);

        $this->addCollector(new CDebug_DataCollector_ExceptionsCollector());
        $this->addCollector(new CDebug_DataCollector_ModelCollector());

        if ($this->shouldCollect('cache')) {
            $cacheCollector = new CDebug_DataCollector_CacheCollector();
            CEvent::dispatcher()->subscribe($cacheCollector);
            $this->addCollector($cacheCollector);
        }

        CFBenchmark::onStopCallback(function ($name, $data) use ($timeDataCollector) {
            $timeDataCollector->addMeasure($name, carr::get($data, 'start'), carr::get($data, 'stop'));
        });
        $completedBenchmarks = CFBenchmark::completed();
        foreach ($completedBenchmarks as $name => $data) {
            $timeDataCollector->addMeasure($name, carr::get($data, 'start'), carr::get($data, 'stop'));
        }

        $this->startMeasure('application', 'Application');

        //if (CHelper::request()->isAjax()) {
        //$this->sendDataInHeaders(true);
        //}

        //$this->renderer->setBindAjaxHandlerToXHR(true);
        //$renderer->setIncludeVendors($this->app['config']->get('debugbar.include_vendors', true));
        //$renderer->setBindAjaxHandlerToXHR($app['config']->get('debugbar.capture_ajax', true));
        $this->booted = true;
    }

    public function populateAssets() {
        $this->renderer->populateAssets();
        $this->renderer->apply();
    }

    /**
     * Starts a measure.
     *
     * @param string $name  Internal name, used to stop the measure
     * @param string $label Public name
     */
    public function startMeasure($name, $label = null) {
        if ($this->hasCollector('time')) {
            /** @var CDebug_DataCollector_TimeDataCollector $collector */
            $collector = $this->getCollector('time');
            $collector->startMeasure($name, $label);
        }
    }

    /**
     * Adds an exception to be profiled in the debug bar.
     *
     * @param Exception $e
     *
     * @deprecated in favor of addThrowable
     */
    public function addException(Exception $e) {
        return $this->addThrowable($e);
    }

    /**
     * Adds an exception to be profiled in the debug bar.
     *
     * @param Exception $e
     */
    public function addThrowable($e) {
        if ($this->hasCollector('exceptions')) {
            /** @var CDebug_DataCollector_ExceptionsCollector $collector */
            $collector = $this->getCollector('exceptions');
            $collector->addThrowable($e);
        }
    }
}
