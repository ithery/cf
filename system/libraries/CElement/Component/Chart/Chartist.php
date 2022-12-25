<?php

class CElement_Component_Chart_Chartist extends CElement_Component_Chart {
    public function __construct() {
        parent::__construct();
        $this->wrapper = $this->addDiv('div-' . $this->id);
        CManager::instance()->registerModule('chartist');
    }

    protected function build() {
        parent::build();
        $this->addClass('cchart cchart-chartist');
        $this->buildData();
    }

    public function buildData() {
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

        $data = $this->data;
        if ($this->type == CChart::TYPE_PIE) {
            $data = carr::first($data);
        }

        $js->append('
            setTimeout(()=>{
	    	new Chartist.' . ucfirst(strtolower($this->type)) . "('#" . $this->wrapper->id() . "', {
	    		labels: " . c::json($this->labels) . ',
	    		series: ' . c::json($data) . "
	    	}, $.parseJSON('" . json_encode($options) . "'));
        },1000);
	    ")->br();

        return $js->text();
    }
}
