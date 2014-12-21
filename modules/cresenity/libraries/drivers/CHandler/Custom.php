<?php defined('SYSPATH') OR die('No direct access allowed.');

class CHandler_Custom_Driver extends CHandler_Driver {

	

	protected $target;
	protected $js;
	
	public function __construct($owner,$event,$name){
		parent::__construct($owner,$event,$name);
		
		
	}
	
	
	public function set_js($js) {
		
		$this->js = $js;
		
		return $this;
	}
	
	
	
	public function script() {
		$js = parent::script();
		$js .= $this->js;
		// $js.= "
			// $.cresenity.reload('".$this->target."','".$this->generated_url()."','".$this->method."',".$data_addition.");
		// ";
		
		return $js;
			
	}
	
}