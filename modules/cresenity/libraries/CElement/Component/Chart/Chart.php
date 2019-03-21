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