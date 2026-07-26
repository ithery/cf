<?php

abstract class CElement_Component_Chart extends CElement_Component {
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array|null
     */
    protected $labels;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var int|string|null
     */
    protected $width;

    /**
     * @var int|string|null
     */
    protected $height;

    /**
     * @var array|null
     */
    protected $options;

    /**
     * @var CChart_ChartAbstract|null
     */
    protected $chart;

    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id = '') {
        parent::__construct($id);
        $this->type = 'line';
        $this->data = [];
    }

    /**
     * @param string $type
     * @param string $id
     *
     * @return CElement_Component_Chart
     */
    public static function factory($type = 'Chart', $id = '') {
        $className = 'CElement_Component_Chart_' . ucfirst(strtolower($type));

        return new $className($id);
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * @param array $labels
     *
     * @return $this
     */
    public function setLabels(array $labels) {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function addRawData(array $data) {
        $this->data[] = $data;

        return $this;
    }

    /**
     * @param array       $data
     * @param null|string $label
     *
     * @return $this
     */
    public function addData(array $data, $label = null) {
        $this->data[] = [
            'data' => $data,
            'label' => $label,
        ];

        return $this;
    }

    /**
     * @param int|string $width
     *
     * @return $this
     */
    public function setWidth($width) {
        $this->width = $width;

        return $this;
    }

    /**
     * @param int|string $height
     *
     * @return $this
     */
    public function setHeight($height) {
        $this->height = $height;

        return $this;
    }

    /**
     * @param CChart_ChartAbstract $chart
     *
     * @return void
     */
    private function updateTypeFromChart(CChart_ChartAbstract $chart) {
        if ($chart instanceof CChart_Chart_BarChart) {
            $this->type = CChart::TYPE_BAR;
        } elseif ($chart instanceof CChart_Chart_PieChart) {
            $this->type = CChart::TYPE_PIE;
        } else {
            $this->type = CChart::TYPE_LINE;
        }
    }

    /**
     * @param CChart_ChartAbstract $chart
     *
     * @return $this
     */
    public function setChart(CChart_ChartAbstract $chart) {
        $this->chart = $chart;
        $this->updateTypeFromChart($chart);
        $this->labels = $chart->getDataLabels();
        $seriesLabels = $chart->getSeriesLabels();
        $dataOptions = $chart->getDataOptions();
        $series = [];
        $colors = $chart->getColors();
        foreach ($chart->getValues() as $index => $serie) {
            $dataset = [];
            $dataset['data'] = $serie;
            //$dataset['fill'] = false;
            $label = carr::get($seriesLabels, $index);
            $options = carr::get($dataOptions, $index, []);
            if ($label) {
                $dataset['label'] = $label;
            }
            // $dataset['fill'] = false;

            if ($chart instanceof CChart_Chart_PieChart) {
                while (count($colors) < count($serie)) {
                    $colors[] = CColor::random()->toRgba();
                }
                $dataset['borderColor'] = c::collect($colors)->map(function ($color) {
                    return $this->colorToRgba($color);
                })->all();
                $dataset['backgroundColor'] = c::collect($colors)->map(function ($color) {
                    return $this->colorToRgba($color);
                })->all();
            } else {
                $randColor = CColor::random()->toRgba();
                $color = carr::get($colors, $index) ?: $randColor;
                $backgroundColor = carr::get($colors, $index) ?: $randColor->fadeOut(80);
                $dataset['borderColor'] = $this->colorToRgba($color);
                $dataset['backgroundColor'] = $this->colorToRgba($backgroundColor);
            }
            $series[] = array_merge($dataset, $options);
        }
        $this->data = $series;

        return $this;
    }

    /**
     * @param null|string $color
     * @param float       $opacity
     *
     * @return string
     */
    protected function getColor($color = null, $opacity = 1.0) {
        if (!$color) {
            return 'rgba(' . mt_rand(0, 255) . ', ' . mt_rand(0, 255) . ', ' . mt_rand(0, 255) . ', ' . $opacity . ')';
        } else {
            preg_match_all("([\d\.]+)", $color, $matches);
            $opacity = $opacity ?: $matches[0][3];

            return 'rgba(' . $matches[0][0] . ', ' . $matches[0][1] . ', ' . $matches[0][2] . ', ' . $opacity . ')';
        }
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options) {
        $this->options = $options;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption($key, $value) {
        carr::set($this->options, $key, $value);

        return $this;
    }

    /**
     * @param mixed $color
     *
     * @return string
     */
    protected function colorToRgba($color) {
        if ($color instanceof CColor_FormatAbstract) {
            return 'rgba(' . implode(', ', $color->toRgba()->values()) . ')';
        }

        return $color;
    }
}
