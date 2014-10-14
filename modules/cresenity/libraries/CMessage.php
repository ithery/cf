<?php
class CMessage extends CRenderable {
	protected $type;
	protected $message;
	
	public function __construct($id) {
		parent::__construct($id);
		
		$this->type="error";
		$this->message = "";
		
		
	}
	
	public function set_type($type) {
		$this->type = $type;
		return $this;
	}
	public function set_message($msg) {
		$this->message = $msg;
		return $this;
	}
	public static function factory($id) {
		return new CMessage($id);
	}
	public function html($indent=0) {
		$html = new CStringBuilder();
		$html->set_indent($indent);
		$class = ""; $header = "";
		switch($this->type) {
			case "warning": $class=" alert-warning"; $header = "Warning!"; break;
			case "info": $class=" alert-info"; $header = "Info!"; break;
			case "success": $class=" alert-success"; $header = "Success!"; break;
			default : $class=" alert-error"; $header = "Error!"; break;
		
		}
		$html->appendln('<div class="alert '.$class.'">')->inc_indent()->br();
		$html->appendln('<a class="close" data-dismiss="alert">&times;</a>')->br();
		$html->appendln('<strong>'.$header.'</strong> '.$this->message)->br();
		
		$html->dec_indent()->appendln('</div>')->br();
		
		
						
					
		return $html->text();	
	}
	public function js($indent=0) {
		return "";
	}
}