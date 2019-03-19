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
	    // $this->setAttr('width', $this->width);
	    // $this->setAttr('height', $this->height);
	}

	public function js($indent = 0) {
	    $js = new CStringBuilder();
	    $js->setIndent($indent);
	    $js->append(parent::js($indent))->br();

	    $js->append("
	    	new Chart($('#" . $this->id . "'), {
	    		type: '" . $this->type . "',
	    		data: " . $this->data . ",
	    		options: {},
    		})
	    ")->br();
	    
	    return $js->text();
	}
}