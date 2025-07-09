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
     * Dataset values.
     * Every array entry is a dataset.
     *
     * @var array
     */
    protected $values = [];

    protected $seriesLabels = [];

    protected $dataLabels = [];

    /**
     * Dataset options.
     * Every array entry is a dataset.
     * Can be used to overwrite dataset values.
     *
     * @var array
     */
    protected $dataOptions = [];

    /**
     * @var array<CColor_FormatAbstract>
     */
    protected $colors = [];

    /**
     * Width of the chart.
     *
     * @var int
     */
    private $width;

    /**
     * Height of the chart.
     *
     * @var int
     */
    private $height;

    /**
     * @var bool
     */
    private $isShowLegend = true;

    private $legendPosition = CChart::POSITION_RIGHT;

    /**
     * X Axis.
     *
     * @var int
     */
    private $xAxis;

    /**
     * Y Axis.
     *
     * @var int
     */
    private $yAxis;

    public function __construct($width = 500, $height = 500) {
        $this->width = $width;
        $this->height = $height;
    }

    public function setWidth($width) {
        $this->width = $width;

        return $this;
    }

    public function getWidth() {
        return $this->width;
    }

    public function setHeight($height) {
        $this->height = $height;

        return $this;
    }

    public function getHeight() {
        return $this->height;
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
     * @param mixed  $options
     *
     * @return $this
     */
    public function addSeries(array $data, $label = null, $options = []) {
        $this->seriesLabels[] = $label;

        $this->values[] = $data;

        $this->dataOptions[] = $options;
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

    public function getSeriesLabels() {
        return $this->seriesLabels;
    }

    public function getDataOptions() {
        return $this->dataOptions;
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
