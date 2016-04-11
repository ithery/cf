<?php defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Handler_Submit extends CMobile_HandlerDriver {

	

	protected $target;
	protected $method;
	protected $content;
	protected $param;
	
	public function __construct($owner,$event,$name){
		parent::__construct($owner,$event,$name);
		$this->method = "get";
		$this->target = "";
		$this->content = CHandlerElement::factory();
		$this->form_id = "";
	}

	
	
	public function set_target($target) {
		
		$this->target = $target;
		
		return $this;
	}
	
	public function set_method($method) {
		$this->method = $method;
	}
	public function set_form($form_id) {
		$this->form_id = $form_id;
		return $this;
	}
	
	public function content() {
		return $this->content;
	}
	
	public function script() {
		$js = parent::script();
		if(strlen($this->form_id)==0) {
			$js.= "
				$('#".$this->owner."').closest('form').submit();;
			";
		} else {
			$js.= "
				$('#".$this->form_id."').submit();;
			";
		}
		
		return $js;
			
	}
	
}