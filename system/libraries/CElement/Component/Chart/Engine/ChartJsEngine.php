<?php

class CElement_Component_Chart_Engine_ChartJsEngine extends CElement_Component_Chart_EngineAbstract {
    /**
     * @return Closure
     */
    public function getBuildElementCallback() {
        $buildCallback = function () {
            /** @var CElement_Component_Chart $this */
            $this->wrapper = $this->addCanvas()->addClass('cchart cchart-chart');
            CManager::instance()->registerModule('chartjs');
            $this->addClass('cchart-container');
        };

        return $buildCallback;
    }

    protected function colorToRgba($color) {
        if ($color instanceof CColor_FormatAbstract) {
            return 'rgba(' . implode(', ', $color->toRgba()->values()) . ')';
        }

        return $color;
    }

    protected function buildRawData(CChart_ChartAbstract $chart, $rawData) {
        $temp = $rawData;
        $data = [];

        $data['labels'] = $chart->getSeriesLabels();
        $data['datasets'] = [];

        foreach ($temp as $value) {
            $label = carr::get($value, 'label');

            $dataset = [];
            $dataset['data'] = carr::get($value, 'data', []);
            $dataset['fill'] = carr::get($value, 'fill', false);

            if ($label) {
                $dataset['label'] = $label;
            }

            $randColor = CColor::random()->toRgba();
            $color = carr::get($value, 'color') ?: $randColor;
            $backgroundColor = carr::get($value, 'backgroundColor') ?: $randColor->fadeOut(80);

            $dataset['borderColor'] = $this->colorToRgba($color);
            $dataset['backgroundColor'] = $this->colorToRgba($backgroundColor);
            $tension = carr::get($value, 'tension');

            if (strlen($tension) > 0) {
                $dataset['tension'] = $tension;
            }

            $data['datasets'][] = $dataset;
        }

        return $data;
    }

    protected function buildData(CChart_ChartAbstract $chart, array $rawData) {
        if (count($rawData) > 0) {
            return $this->buildRawData($chart, $rawData);
        }
        $data = [];
        $data['labels'] = $chart->getDataLabels();
        $seriesLabels = $chart->getSeriesLabels();

        $colors = $chart->getColors();
        foreach ($chart->getValues() as $index => $serie) {
            $dataset = [];
            $dataset['data'] = $serie;
            //$dataset['fill'] = false;
            $label = carr::get($seriesLabels, $index);
            if ($label) {
                $dataset['label'] = $label;
            }
            $dataset['fill'] = false;

            $randColor = CColor::random()->toRgba();
            $color = carr::get($colors, $index) ?: $randColor;
            $backgroundColor = carr::get($colors, $index) ?: $randColor->fadeOut(80);

            $dataset['borderColor'] = $this->colorToRgba($color);
            $dataset['backgroundColor'] = $this->colorToRgba($backgroundColor);
            $series[] = $dataset;
        }
        $data['datasets'] = $series;

        return $data;
    }

    public function js(CElement_Component_Chart $element, $indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);

        $options = $element->getOptions();
        $width = $element->getWidth();
        $height = $element->getHeight();
        $wrapperId = $element->getWrapper()->id();
        if ($width || $height) {
            $options['maintainAspectRatio'] = false;
        }
        $data = $this->buildData($element->getChart(), $element->getRawData());

        $js->append('
            var chartOptions =' . c::json($options) . ';

	    	var chart' . $element->getWrapper()->id() . " = new Chart($('#" . $element->getWrapper()->id() . "'), {
	    		type: '" . $element->getType() . "',
	    		data: " . c::json($data) . ',
	    		options: chartOptions,


    		});
	    ')->br();

        if ($width) {
            $js->append('chart' . $wrapperId . ".canvas.parentNode.style.width = '" . $width . "px';")->br();
        }
        if ($height) {
            $js->append('chart' . $wrapperId . ".canvas.parentNode.style.height = '" . $height . "px';")->br();
        }

        $js->append("$('#" . $wrapperId . "').data('capp-chart',chart" . $wrapperId . ');');

        return $js->text();
    }
}
