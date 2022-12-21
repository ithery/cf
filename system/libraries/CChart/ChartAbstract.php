<?php
/**
 * @see CChart
 */
class CChart_ChartAbstract {
    /**
     * Chart title.
     *
     * @var string
     */
    protected $title;

    /**
     * Data set values.
     * Every array entry is a data set.
     *
     * @var array
     */
    protected $values = [];

    protected $seriesLabels = [];

    protected $dataLabels = [];

    /**
     * @var array<CColor_FormatAbstract>
     */
    protected $colors = [];

    /**
     * @var bool
     */
    private $isShowLegend = true;

    private $legendPosition = CChart::POSITION_RIGHT;

    public function __construct() {
    }

    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    /**
     * @param array  $data
     * @param string $label
     *
     * @return $this
     */
    public function addSeries(array $data, $label = null) {
        $this->seriesLabels[] = $label;

        $this->values[] = $data;
    }

    public function setColors(array $colors) {
        $colors = c::collect($colors)->map(function ($color) {
            if (!($color instanceof CColor_FormatAbstract)) {
                $color = CColor::create($color);
            }

            return $color;
        })->all();
        $this->colors = $colors;

        return $this->colors;
    }

    public function getValues() {
        return $this->values;
    }

    public function getDataLabels() {
        return $this->dataLabels;
    }

    public function setDataLabels(array $labels) {
        $this->dataLabels = $labels;

        return $this;
    }

    public function setSeriesLabels(array $labels) {
        $this->seriesLabels = $labels;

        return $this;
    }

    public function getSeriesLabels() {
        return $this->seriesLabels;
    }

    /**
     * @return array<CColor_FormatAbstract>
     */
    public function getColors() {
        return $this->colors;
    }

    public function xAxis() {
        return $this->xAxis;
    }

    public function yAxis() {
        return $this->yAxis;
    }

    public function showLegend() {
        $this->isShowLegend = true;
    }

    public function hideLegend() {
        $this->isShowLegend = false;
    }

    public function isShowLegend() {
        return $this->isShowLegend;
    }

    public function getLegendPosition() {
        return $this->legendPosition;
    }

    public function setLegendPosition($position) {
        $this->legendPosition = $position;

        return $this;
    }
}
