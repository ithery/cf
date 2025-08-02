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
     * Creates a Google chart engine instance.
     *
     * @param CImage_Chart_Builder $builder
     * @return CImage_Chart_Engine_GoogleEngine
     */
    public function createGoogleEngine(CImage_Chart_Builder $builder) {
        return new CImage_Chart_Engine_GoogleEngine($builder);
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
