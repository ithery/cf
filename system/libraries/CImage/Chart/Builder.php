<?php

class CImage_Chart_Builder {
    //BAR
    const CHT_BHS = 'bhs';

    const CHT_BVS = 'bvs';

    const CHT_BVO = 'bvo';

    const CHT_BHG = 'bhg';

    const CHT_BVG = 'bvg';

    //LINE
    const CHT_LC = 'lc';

    const CHT_LS = 'ls';

    const CHT_LXY = 'lxy';

    //PIE
    const CHT_P = 'p';

    const CHT_P3 = 'p3';

    const CHT_PC = 'pc';

    protected $engine = 'google';

    protected $width;

    protected $height;

    protected $chart;

    public function __construct($width, $height) {
        $this->width = $width;
        $this->height = $height;
        $this->engine = 'google';
    }

    public function setChart(CChart_ChartAbstract $chart) {
        $this->chart = $chart;
    }

    public function getChart() {
        return $this->chart;
    }

    public function getWidth() {
        return $this->width;
    }

    public function getHeight() {
        return $this->height;
    }

    private function setWidth($width) {
        $this->width = $width;

        return $this;
    }

    private function setHeight($height) {
        $this->height = $height;

        return $this;
    }

    public function setEngine($engine) {
        $this->engine = $engine;

        return $this;
    }

    /**
     * Sets chart dimensions.
     *
     * @param $width Integer
     * @param $height Integer
     */
    public function setSize($width, $height) {
        $this->setWidth($width);
        $this->setHeight($height);
    }

    public function toUri() {
        $engine = CImage_Chart_Manager::instance()->resolveEngine($this->engine, $this);

        return $engine->toUri();
    }
}
