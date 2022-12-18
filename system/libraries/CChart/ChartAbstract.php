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

    protected $labels = [];

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

    public function getValues() {
        return $this->values;
    }

    public function getLabels() {
        return $this->labels;
    }
}
