<?php

/**
 * 
 */
class CElement_Component_Chart_Morris extends CElement_Component_Chart
{
	public function __construct()
	{
		parent::__construct();
		CManager::instance()->registerModule('eve');
		CManager::instance()->registerModule('raphael');
		CManager::instance()->registerModule('morris');
	}

	protected function build() {
	    parent::build();
	    $this->addClass('cchart cchart-morris');
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