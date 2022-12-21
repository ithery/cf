<?php

class CElement_Component_Chart extends CElement_Component {
    const ENGINE_CHARTJS = 'chartjs';

    protected $type;

    protected $rawData;

    protected $width;

    protected $height;

    protected $options;

    protected $chart;

    protected $engine;

    public function __construct($id = '') {
        parent::__construct($id);
        $this->type = CChart::TYPE_LINE;
        $this->data = [];
        $this->chart = CChart::lineChart();
        $this->engine = self::ENGINE_CHARTJS;
        $this->rawData = [];
        $this->options = [];
    }

    public function setEngine($engine) {
        $this->engine = $engine;

        return $this;
    }

    public function addSeries($data, $label = null) {
        $this->chart->addSeries($data, $label);

        return $this;
    }

    public function setDataLabels(array $labels) {
        $this->chart->setDataLabels($labels);

        return $this;
    }

    public function setSeriesLabels(array $labels) {
        $this->chart->setSeriesLabels($labels);

        return $this;
    }

    public function setColors(array $colors) {
        $this->chart->setColors($colors);

        return $this;
    }

    public function setChart(CChart_ChartAbstract $chart) {
        $this->chart = $chart;

        return $this;
    }

    /**
     * @return CChart_ChartAbstract
     */
    public function getChart() {
        return $this->chart;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    public function setLabels(array $labels) {
        return $this->setSeriesLabels($labels);

        return $this;
    }

    public function setWidth($width) {
        $this->width = $width;

        return $this;
    }

    public function setHeight($height) {
        $this->height = $height;

        return $this;
    }

    public function getWidth() {
        return $this->width;
    }

    public function getHeight() {
        return $this->height;
    }

    public function addRawData(array $data) {
        $this->rawData[] = $data;

        return $this;
    }

    public function getRawData() {
        return $this->rawData;
    }

    public function addData(array $data, $label = null) {
        return $this->addSeries($data, $label);
    }

    protected function getColor($color = null, $opacity = 1.0) {
        if (!$color) {
            return 'rgba(' . mt_rand(0, 255) . ', ' . mt_rand(0, 255) . ', ' . mt_rand(0, 255) . ', ' . $opacity . ')';
        } else {
            preg_match_all("([\d\.]+)", $color, $matches);
            $opacity = $opacity ?: $matches[0][3];

            return 'rgba(' . $matches[0][0] . ', ' . $matches[0][1] . ', ' . $matches[0][2] . ', ' . $opacity . ')';
        }
    }

    public function setOptions(array $options) {
        $this->options = $options;

        return $this;
    }

    public function setOption($key, $value) {
        carr::set($this->options, $key, $value);

        return $this;
    }

    public function getOptions() {
        return $this->options;
    }

    protected function build() {
        $manager = CElement_Component_Chart_Manager::instance();
        $engine = $manager->resolveEngine($this->engine);
        $callback = $engine->getBuildElementCallback();
        $callback->call($this);
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $js->append(parent::js($indent))->br();
        $manager = CElement_Component_Chart_Manager::instance();
        $engine = $manager->resolveEngine($this->engine);
        $js->append($engine->js($this, $indent));

        return $js->text();
    }

    public function getWrapper() {
        return $this->wrapper;
    }
}
