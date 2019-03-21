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

	    switch ($this->type) {
	    	case 'line':
	    		$options['series'] = [
	    			'lines' => [
	    				'show' => true,
	    			],
	    			'points' => [
	    				'show' => true,
	    				'radius' => 4,
	    			],
	    		];

	    		$options['tooltip'] = [
	    			'show' => true,
	    		];
	    		break;
	    	case 'bar':

	    		break;
	    }

	    $data = [
	    	[
	    		'label' => 'Foo',
	    		'data' => [
	    			[10, 1],
	    			[17, -14],
	    			[30, 5],
	    		],
	    	],
	    	[
	    		'label' => 'Bar',
	    		'data' => [
	    			[11, 13],
	    			[19, 11],
	    			[30, -7],
	    		],
	    	]
	    ];

	    $js->append("
	    	var chart" . $this->id . " = $.plot($('#" . $this->id . "'),
	    		[ { label: 'Foo', data: [ [10, 1], [17, -14], [30, 5] ] },
	    		  { label: 'Bar', data: [ [11, 13], [19, 11], [30, -7] ] }
	    		]
	    	);
	    ")->br();

	    $js->append("console.log('chart" . $this->id . ":', chart" . $this->id . ");")->br();
	    
	    return $js->text();
	}
}