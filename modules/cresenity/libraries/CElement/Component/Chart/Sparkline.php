<?php

/**
 * 
 */
class CElement_Component_Chart_Sparkline extends CElement_Component_Chart
{
	public function __construct()
	{
		parent::__construct();
		CManager::instance()->registerModule('sparkline');
	}

	protected function build() {
	    parent::build();
	    $this->addClass('cchart cchart-sparkline');
	    $this->buildData();
	}

	public function buildData()
	{
		$temp = $this->data;
		$this->data = [];

		foreach ($temp as $value) {
			foreach (carr::get($value, 'data', []) as $v) {
				$this->data[] = $v;
			}
		}
	}

	public function js($indent = 0) {
	    $js = new CStringBuilder();
	    $js->setIndent($indent);
	    $js->append(parent::js($indent))->br();

	    $options = [];
	    $options['type'] = $this->type;
	    if ($this->width) {
	    	$options['width'] = $this->width;
	    	$options['width'] = '100%';
	    }
	    if ($this->height) {
		    $options['height'] = $this->height;
		    $options['height'] = '100px';
	    }

	    $js->append("
	    	$('#" . $this->id . "').sparkline2($.parseJSON('" . json_encode($this->data) . "'), $.parseJSON('" . json_encode($options) . "'))
	    ")->br();
	    
	    return $js->text();
	}
}