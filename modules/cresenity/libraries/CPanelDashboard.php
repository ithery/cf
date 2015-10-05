<?php

class CPanelDashboard extends CObject {
	protected $_engine;
    protected function __construct($engine_name) {
		$path = dirname(__FILE__) . DS . "PanelDashboard" . DS . $engine_name . EXT;
		require_once $path;
		$this->_engine = $file_name::factory();
	}
	public function factory($engine_name){
        return new CPanelDashboard($engine_name);
	}
	
	protected function test(){
		$_engine->test();
	}

}

