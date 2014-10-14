<?php defined('SYSPATH') OR die('No direct access allowed.');

class CHandler_Dialog_Driver extends CHandler_Driver {

	

	protected $target;
	protected $method;
	protected $content;
	protected $param;
	protected $title;
	protected $actions;
	
	public function __construct($owner,$event,$name){
		parent::__construct($owner,$event,$name);
		$this->method = "get";
		$this->target = "";
		$this->content = CHandlerElement::factory();
		$this->actions = CActionList::factory();
	}

	public function set_title($title) {
		$this->title = $title;
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
		
		if(strlen($this->target)==0) {
			$this->target = "modal_opt_".$this->event."_".$this->owner."_dialog";
		}
		/*
		$js.= "
			var modal_opt_".$this->event."_".$this->owner." = {
			  id: 'modal_opt_".$this->event."_".$this->owner."_dialog', // id which (if specified) will be added to the dialog to make it accessible later 
			  autoOpen: true , // Should the dialog be automatically opened?
			  title: '".$this->title."', 
			  content: '".$this->generated_url()."', 
			  buttons: {
				
			  }, 
			  closeOnOverlayClick: true , // Should the dialog be closed on overlay click?
			  closeOnEscape: true , // Should the dialog be closed if [ESCAPE] key is pressed?
			  removeOnClose: true , // Should the dialog be removed from the document when it is closed?
			  showCloseHandle: true , // Should a close handle be shown?
			  initialLoadText: '' // Text to be displayed when the dialogs contents are loaded
			}
			jQuery('<div/>').dialog2(modal_opt_".$this->event."_".$this->owner.");
		";
		*/
		$js.= "
			$.cresenity.show_dialog('".$this->target."','".$this->generated_url()."','".$this->method."','".$this->title."');
		";
		return $js;
			
	}
	
}