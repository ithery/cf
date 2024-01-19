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

    protected $engine = 'default';

    protected $width;

    protected $height;

    protected $chart;

    protected $topMargin = 50;

    protected $leftMargin = 50;

    protected $rightMargin = 50;

    protected $bottomMargin = 50;

    /**
     * @param int $width
     * @param int $height
     */
    public function __construct($width, $height) {
        $this->width = $width;
        $this->height = $height;
        $this->engine = 'default';
    }

    public function setChart(CChart_ChartAbstract $chart) {
        $this->chart = $chart;

        return $this;
    }

    /**
     * @return null|CChart_ChartAbstract
     */
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

        return $this;
    }

    public function setTopMargin($margin) {
        $this->topMargin = $margin;

        return $this;
    }

    public function setBottomMargin($margin) {
        $this->bottomMargin = $margin;

        return $this;
    }

    public function setLeftMargin($margin) {
        $this->leftMargin = $margin;

        return $this;
    }

    public function setRightMargin($margin) {
        $this->rightMargin = $margin;

        return $this;
    }

    public function getTopMargin() {
        return $this->topMargin;
    }

    public function getLeftMargin() {
        return $this->leftMargin;
    }

    public function getBottomMargin() {
        return $this->bottomMargin;
    }

    public function getRightMargin() {
        return $this->rightMargin;
    }

    public function setMargin($top = 0, $left = 0, $bottom = 0, $right = 0) {
        $this->setTopMargin($top);
        $this->setLeftMargin($left);
        $this->setBottomMargin($bottom);
        $this->setRightMargin($right);

        return $this;
    }

    public function toUri() {
        $engine = CImage_Chart_Manager::instance()->resolveEngine($this->engine, $this);

        return $engine->toUri();
    }
}
