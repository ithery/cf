<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 1:04:27 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDebug_Bar extends CDebug_AbstractBar {

    /**
     * True when booted.
     *
     * @var bool
     */
    protected $booted;

    /**
     * True when enabled, false disabled an null for still unknown
     *
     * @var bool
     */
    protected $enabled;

    public function __construct($options = array()) {
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

    public function enable() {
        $this->enabled = true;
        if (!$this->booted) {
            $this->boot();
        }
    }

    /**
     * Boot the debugbar (add collectors, renderer and listener)
     */
    public function boot() {
        if ($this->booted) {
            return;
        }

        $this->addCollector(new CDebug_DataCollector_PhpInfoCollector());
        $this->addCollector(new CDebug_DataCollector_MemoryCollector());
        $this->addCollector(new CDebug_DataCollector_LocalizationCollector());
        $this->addCollector(new CDebug_DataCollector_MessagesCollector());
        $this->addCollector(new CDebug_DataCollector_RequestDataCollector());
        $this->addCollector(new CDebug_DataCollector_TimeDataCollector());
        $this->addCollector(new CDebug_DataCollector_FilesCollector());

        $queryCollector = new CDebug_DataCollector_QueryCollector();


        $queryCollector->setRenderSqlWithParams(true);
        $this->addCollector($queryCollector);

        $this->addCollector(new CDebug_DataCollector_ExceptionsCollector());
        $this->startMeasure('application', 'Application');


        //if (CHelper::request()->isAjax()) {
            //$this->sendDataInHeaders(true);
        //}

        $this->renderer->populateAssets();
        $this->renderer->apply();
        //$this->renderer->setBindAjaxHandlerToXHR(true);
        //$renderer->setIncludeVendors($this->app['config']->get('debugbar.include_vendors', true));
        //$renderer->setBindAjaxHandlerToXHR($app['config']->get('debugbar.capture_ajax', true));
        $this->booted = true;
    }

    /**
     * Starts a measure
     *
     * @param string $name Internal name, used to stop the measure
     * @param string $label Public name
     */
    public function startMeasure($name, $label = null) {
        if ($this->hasCollector('time')) {
            /* @var CDebug_DataCollector_TimeDataCollector $collector  */
            $collector = $this->getCollector('time');
            $collector->startMeasure($name, $label);
        }
    }

    /**
     * Adds an exception to be profiled in the debug bar
     *
     * @param Exception $e
     * @deprecated in favor of addThrowable
     */
    public function addException(Exception $e) {
        return $this->addThrowable($e);
    }

    /**
     * Adds an exception to be profiled in the debug bar
     *
     * @param Exception $e
     */
    public function addThrowable($e) {
        if ($this->hasCollector('exceptions')) {
            /** @var \DebugBar\DataCollector\ExceptionsCollector $collector */
            $collector = $this->getCollector('exceptions');
            $collector->addThrowable($e);
        }
    }

}
