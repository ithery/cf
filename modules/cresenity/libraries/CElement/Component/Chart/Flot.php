<?php

/**
 * 
 */
class CElement_Component_Chart_Flot extends CElement_Component_Chart
{
	public function __construct()
	{
		parent::__construct();
		CManager::instance()->registerModule('flot');
	}

	protected function build() {
	    parent::build();
	    $this->addClass('cchart cchart-flot');
	    $this->customCss('width', '500px');
	    $this->customCss('height', '500px');
	    $this->buildData();
	}

	public function buildData() {
		$temp = $this->data;
		$this->data = [];

		foreach ($temp as $value) {
			$data = [];
			$data['label'] = carr::get($value, 'label', '');
			$data['data'] = [];

			foreach (carr::get($value, 'data', []) as $k => $d) {
				$data['data'][] = [carr::get($this->labels, $k), $d];
			}

			$this->data[] = $data;
		}
	}

	public function js($indent = 0) {
	    $js = new CStringBuilder();
	    $js->setIndent($indent);
	    $js->append(parent::js($indent))->br();

	    $options = [];
	    $options['grid'] = [
	    	'color' => '#aaaaaa',
	    	'borderColor' => '#eeeeee',
	    	'borderWidth' => 1,
	    	'hoverable' => true,
	    	'clickable' => true,
	    ];

	    $options['colors'] = [];
	    foreach ($this->data as $value) {
	    	$options['colors'][] = $this->getColor();
	    }

	    $options['tooltip'] = [
	    	'show' => true,
	    ];

	    switch ($this->type) {
	    	case 'line':
	    		$options['series'] = [
	    			'shadowSize' => 0,
	    			'lines' => [
	    				'show' => true,
	    			],
	    			'points' => [
	    				'show' => true,
	    				'radius' => 4,
	    			],
	    		];
	    		break;
	    	case 'bar':
	    		$options['series'] = [
	    			'shadowSize' => 0,
	    			'bars' => [
	    				'show' => true,
	    				'barWidth' => .6,
	    				'align' => 'center',
	    				'lineWidth' => 1,
	    				'fill' => 0.25,
	    			],
	    			'points' => [
	    				'show' => true,
	    				'radius' => 4,
	    			],
	    		];
	    		break;
	    }

	    $js->append("
	    	var chart" . $this->id . " = $.plot($('#" . $this->id . "'),
	    		$.parseJSON('" . json_encode($this->data) . "'),
	    		$.parseJSON('" . json_encode($options) . "')
	    	);
	    ")->br();

	    $js->append("console.log('chart" . $this->id . ":', chart" . $this->id . ");")->br();
	    
	    return $js->text();
	}
}