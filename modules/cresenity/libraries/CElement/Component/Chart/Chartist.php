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