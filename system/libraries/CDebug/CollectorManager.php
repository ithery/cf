<?php

class CDebug_CollectorManager {
    private static $instance;

    /**
     * @var CDebug_Collector_Exception
     */
    private $exception;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @return CDebug_Collector_Exception
     */
    public function exception() {
        if ($this->exception == null) {
            $this->exception = new CDebug_Collector_Exception();
        }

        return $this->exception;
    }

    public static function allCollectorType() {
        return [CDebug::COLLECTOR_TYPE_DEPRECATED, CDebug::COLLECTOR_TYPE_EXCEPTION, CDebug::COLLECTOR_TYPE_PROFILER];
    }
}
