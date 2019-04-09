<?php

/**
 * 
 */
class CElement_Component_Chart_Chartist extends CElement_Component_Chart
{
	public function __construct()
	{
		parent::__construct();
		CManager::instance()->registerModule('chartist');
	}

	protected function build() {
	    parent::build();
	    $this->addClass('cchart cchart-chartist');
	    $this->buildData();
	}

	public function buildData()
	{
		$temp = $this->data;
		$this->data = [];

		foreach ($temp as $value) {
			$this->data[] = carr::get($value, 'data');
		}
	}

	public function js($indent = 0) {
	    $js = new CStringBuilder();
	    $js->setIndent($indent);
	    $js->append(parent::js($indent))->br();

	    $options = [];

	    $js->append("
	    	new Chartist." . ucfirst(strtolower($this->type)) . "('#" . $this->id . "', {
	    		labels: $.parseJSON('" . json_encode($this->labels) . "'),
	    		series: $.parseJSON('" .  json_encode($this->data) . "')
	    	}, $.parseJSON('" . json_encode($options) . "'));
	    ")->br();
	    
	    return $js->text();
	}
}