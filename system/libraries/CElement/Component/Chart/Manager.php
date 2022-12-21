<?php

class CElement_Component_Chart_Manager {
    private static $instance;

    /**
     * @return CElement_Component_Chart_Manager
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string                   $engine
     *
     * @return CElement_Component_Chart_EngineAbstract
     */
    public function resolveEngine($engine) {
        $method = 'create' . ucfirst($engine) . 'Engine';

        return $this->$method();
    }

    public function createChartJsEngine() {
        return new CElement_Component_Chart_Engine_ChartJsEngine();
    }
}
