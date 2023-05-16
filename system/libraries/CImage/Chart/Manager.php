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

    public function resolveEngine($engine, CImage_Chart_Builder $builder) {
        $method = 'create' . ucfirst($engine) . 'Engine';

        return $this->$method($builder);
    }

    public function createGoogleEngine(CImage_Chart_Builder $builder) {
        return new CImage_Chart_Engine_GoogleEngine($builder);
    }

    public function createDefaultEngine(CImage_Chart_Builder $builder) {
        return new CImage_Chart_Engine_DefaultEngine($builder);
    }
}
