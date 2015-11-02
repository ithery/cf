<?php

class CPanelDashboard extends CObject {
	protected $_engine;
    protected function __construct($engine_name,$app_code=null) {
		if ($app_code == null) {
            $app_code = CF::app_code();
        }
		$path = DOCROOT . 'application' . DS . $app_code . DS .'default'. DS .'libraries' . DS . "PanelDashboard" . DS . $engine_name . EXT;
		require_once $path;
		$this->_engine = $engine_name::factory();
	}
	public function factory($engine_name){
        return new CPanelDashboard($engine_name);
	}
	
	public function render(){
		return $this->_engine->render();
	}

}

