<?php defined('SYSPATH') OR die('No direct access allowed.');

class CHandler_Websocket_Driver extends CHandler_Driver {

	protected $target;
	protected $js;
	protected $param_inputs;
	
	public function __construct($owner,$event,$name){
		parent::__construct($owner,$event,$name);
	}

	public function add_param_input($inputs) {
		if(!is_array($inputs)) {
			$inputs = array($inputs);
		}
		foreach($inputs as $inp) {
			$this->param_inputs[] = $inp;
		}
		return $this;
	}
	
	public function script() {
		$js = parent::script();
		$js .= $this->js;
		$data_addition = '';
		
		foreach($this->param_inputs as $inp) {
			if(strlen($data_addition)>0) $data_addition.=',';
			$data_addition.="'".$inp."':$('#".$inp."').val()";
		}
		$data_addition = '{'.$data_addition.'}';
		$js.= "
			websocket.send(JSON.stringify(" . $data_addition . "));
		";
		
		return $js;
			
	}
	
}