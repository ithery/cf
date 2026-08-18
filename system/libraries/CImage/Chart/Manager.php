<?php

class CImage_Chart_Manager {
    private static $instance;

    /**
     * @return CImage_Chart_Manager
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Resolves a chart engine by given name.
     *
     * @param string $engine
     * @param CImage_Chart_Builder $builder
     * @return CImage_Chart_EngineAbstract
     */
    public function resolveEngine($engine, CImage_Chart_Builder $builder) {
        $method = 'create' . ucfirst($engine) . 'Engine';

        return $this->$method($builder);
    }

    /**
     * Creates a QuickChart.io-backed chart engine instance (drop-in replacement for the
     * long-defunct Google Image Charts API, using the same query parameter format).
     *
     * @param CImage_Chart_Builder $builder
     * @return CImage_Chart_Engine_QuickChartEngine
     */
    public function createQuickchartEngine(CImage_Chart_Builder $builder) {
        return new CImage_Chart_Engine_QuickChartEngine($builder);
    }

    /**
     * Creates a default chart engine instance.
     *
     * @param CImage_Chart_Builder $builder
     * @return CImage_Chart_Engine_DefaultEngine
     */
    public function createDefaultEngine(CImage_Chart_Builder $builder) {
        return new CImage_Chart_Engine_DefaultEngine($builder);
    }
}
