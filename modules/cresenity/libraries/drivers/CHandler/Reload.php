<?php defined('SYSPATH') OR die('No direct access allowed.');

class CHandler_Reload_Driver extends CHandler_Driver {

	

	protected $target;
	protected $method;
	protected $content;
	protected $param;
	
	public function __construct($owner,$event,$name){
		parent::__construct($owner,$event,$name);
		$this->method = "get";
		$this->target = "";
		$this->content = CHandlerElement::factory();
		
	}

	
	
	public function set_target($target) {
		
		$this->target = $target;
		
		return $this;
	}
	
	public function set_method($method) {
		$this->method = $method;
	}
	
	public function content() {
		return $this->content;
	}
	
	public function script() {
		$js = parent::script();
		
		$js.= "
			$.cresenity.reload('".$this->target."','".$this->generated_url()."','".$this->method."');
		";
		
		return $js;
			
	}
	
}