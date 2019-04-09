<?php

/**
 * 
 */
class CElement_Component_Chart_C3 extends CElement_Component_Chart
{
	public function __construct()
	{
		parent::__construct();
		CManager::instance()->registerModule('c3');
	}

	protected function build() {
	    parent::build();
	    $this->addClass('cchart cchart-c3');
	    $this->buildData();
	}

	public function buildData() {
		$temp = $this->data;
		$this->data = [];
		$this->data['columns'] = [];

		foreach ($temp as $value) {
			$this->data['columns'][] = array_merge([carr::get($value, 'label')], carr::get($value, 'data', []));
		}
	}

	public function js($indent = 0) {
	    $js = new CStringBuilder();
	    $js->setIndent($indent);
	    $js->append(parent::js($indent))->br();

	    $color = [];
	    $color['pattern'] = [];

	    foreach ($this->data['columns'] as $value) {
	    	$color['pattern'][] = $this->getColor();
	    }

	    $js->append("
	    	c3.generate({
	    		bindto: $('#" . $this->id . "'),
	    		color: $.parseJSON('" . json_encode($color) . "'),
	    		data: $.parseJSON('" . json_encode($this->data) . "')
	    	});
	    ")->br();
	    
	    return $js->text();
	}
}