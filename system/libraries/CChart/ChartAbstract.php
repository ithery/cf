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

    public function getSeriesLabels() {
        return $this->seriesLabels;
    }

    /**
     * @return array<CColor_FormatAbstract>
     */
    public function getColors() {
        return $this->colors;
    }
}
