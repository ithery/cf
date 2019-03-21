<?php

/**
 * 
 */
class CElement_Component_Chart_Chart extends CElement_Component_Chart
{
	public function __construct()
	{
		parent::__construct();
		CManager::instance()->registerModule('chartjs');
	}

	protected function build() {
	    parent::build();
	    $this->addClass('cchart cchart-chart');
	    $this->buildData();
	}

	public function buildData()
	{
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
			$dataset['borderColor'] = carr::get($value, 'color') ?: $randColor;
			$dataset['backgroundColor'] = $this->getColor($randColor, 0.2);

			$this->data['datasets'][] = $dataset;
		}
	}

	public function js($indent = 0) {
	    $js = new CStringBuilder();
	    $js->setIndent($indent);
	    $js->append(parent::js($indent))->br();

	    $options = [];

	    if ($this->width || $this->height) {
	    	$options['maintainAspectRatio'] = false;
	    }

	    $js->append("
	    	var chart" . $this->id . " = new Chart($('#" . $this->id . "'), {
	    		type: '" . $this->type . "',
	    		data: $.parseJSON('" . json_encode($this->data) . "'),
	    		options: $.parseJSON('" . json_encode($options) . "'),
    		});
	    ")->br();

	    if ($this->width) {
		    $js->append("chart" . $this->id . ".canvas.parentNode.style.width = '" . $this->width . "px';")->br();
	    }
	    if ($this->height) {
		    $js->append("chart" . $this->id . ".canvas.parentNode.style.height = '" . $this->height . "px';")->br();
	    }
	    
	    return $js->text();
	}
}