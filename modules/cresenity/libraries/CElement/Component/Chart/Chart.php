<?php

/**
 * 
 */
class CElement_Component_Chart_Chart extends CElement_Component_Chart {

    public function __construct() {
        parent::__construct();
        $this->wrapper = $this->addCanvas()->addClass('cchart cchart-chart');
        CManager::instance()->registerModule('chartjs');
        $this->options = array();
    }

    protected function build() {
        parent::build();
        $this->addClass('cchart-container');
        $this->buildData();
    }

    public function buildData() {
        $temp = $this->data;
        $this->data = [];

        $this->data['labels'] = $this->labels;
        $this->data['datasets'] = [];

        foreach ($temp as $value) {
            $label = carr::get($value, 'label');

            $dataset = [];
            $dataset['data'] = carr::get($value, 'data', []);
            $dataset['fill'] = carr::get($value, 'fill', false);

            if ($label) {
                $dataset['label'] = $label;
            }

            $randColor = $this->getColor();
            $color = carr::get($value, 'color') ?: $randColor;
            $backgroundColor = carr::get($value, 'backgroundColor') ?: $this->getColor($randColor, 0.2);
            $tension = carr::get($value, 'tension');
            //$backgroundColor = $this->getColor($color, 0.2);
            if (is_array($dataset['data'])) {
                $color=[];
                $backgroundColor=[];
                foreach ($dataset['data'] as $k => $v) {
                    $randColor = $this->getColor();
                    $colorTemp = carr::get($value, 'color');
                    $backgroundColorTemp = carr::get($value, 'backgroundColor');
                    if(is_array($colorTemp)) {
                        $colorTemp = carr::get($colorTemp,$k);
                    }
                    if(is_array($backgroundColorTemp)) {
                        $backgroundColorTemp = carr::get($backgroundColorTemp,$k);
                    }
                    
                    $color[] = $colorTemp;
                    if(strlen($backgroundColorTemp)==0) {
                        $backgroundColorTemp=$this->getColor($colorTemp, 0.2);
                    }
                    $backgroundColor[] = $backgroundColorTemp;
                }
            }
            $dataset['borderColor'] = $color;
            $dataset['backgroundColor'] = $backgroundColor;
            if (strlen($tension) > 0) {
                $dataset['tension'] = $tension;
            }

            $this->data['datasets'][] = $dataset;
        }
    }

    public function buildOptions() {
        return $this->options;
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $js->append(parent::js($indent))->br();

        $options = $this->buildOptions();

        if ($this->width || $this->height) {
            $options['maintainAspectRatio'] = false;
        }

        $js->append("
                var chartOptions =$.parseJSON('" . json_encode($options) . "');

	    	var chart" . $this->wrapper->id . " = new Chart($('#" . $this->wrapper->id . "'), {
	    		type: '" . $this->type . "',
	    		data: $.parseJSON('" . json_encode($this->data) . "'),
	    		options: chartOptions,
                        
                        
    		});
	    ")->br();

        if ($this->width) {
            $js->append("chart" . $this->wrapper->id . ".canvas.parentNode.style.width = '" . $this->width . "px';")->br();
        }
        if ($this->height) {
            $js->append("chart" . $this->wrapper->id . ".canvas.parentNode.style.height = '" . $this->height . "px';")->br();
        }
        
        $js->append("$('#" . $this->wrapper->id . "').data('capp-chart',chart" . $this->wrapper->id . ");");

        return $js->text();
    }

}
