<?php

use DebugBar\DataCollector\TimeDataCollector;

/**
 * @see CDebug
 */
class CDebug_DebugBar extends CDebug_AbstractBar {
    use CDebug_DebugBar_DebugBarTrait_PhpInfoCollectorTrait;
    use CDebug_DebugBar_DebugBarTrait_MessagesCollectorTrait;
    use CDebug_DebugBar_DebugBarTrait_TimeDataCollectorTrait;
    use CDebug_DebugBar_DebugBarTrait_MemoryCollectorTrait;
    use CDebug_DebugBar_DebugBarTrait_ExceptionsCollectorTrait;
    use CDebug_DebugBar_DebugBarTrait_CFCollectorTrait;
    use CDebug_DebugBar_DebugBarTrait_RequestDataCollectorTrait;
    use CDebug_DebugBar_DebugBarTrait_EventCollectorTrait;
    use CDebug_DebugBar_DebugBarTrait_ViewCollectorTrait;
    use CDebug_DebugBar_DebugBarTrait_FilesCollectorTrait;

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
        $this->setupPhpInfoCollector();
        $this->setupMessagesCollector();
        $this->setupTimeDataCollector();
        $this->setupMemoryCollector();
        $this->setupExceptionsCollector();
        $this->setupCFCollector();
        $this->setupRequestDataCollector();
        $this->setupEventCollector();
        $this->setupViewCollector();
        $this->setupFilesCollector();
        $this->addCollector(new CDebug_DebugBar_DataCollector_RenderableCollector());

        $queryCollector = new CDebug_DebugBar_DataCollector_QueryCollector($this->getCollector('time'));

        $queryCollector->setRenderSqlWithParams(true);
        $this->addCollector($queryCollector);

        $this->addCollector(new CDebug_DebugBar_DataCollector_ModelCollector());

        if ($this->shouldCollect('cache')) {
            $cacheCollector = new CDebug_DebugBar_DataCollector_CacheCollector();
            CEvent::dispatcher()->subscribe($cacheCollector);
            $this->addCollector($cacheCollector);
        }

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
            /** @var CDebug_DebugBar_DataCollector_TimeDataCollector $collector */
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
            /** @var CDebug_DebugBar_DataCollector_ExceptionsCollector $collector */
            $collector = $this->getCollector('exceptions');
            $collector->addThrowable($e);
        }
    }
}
