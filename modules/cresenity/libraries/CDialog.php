<?php	
class CDialog extends CFormInput {		
	protected $title;
	
	public function __construct($id) {			
		parent::__construct($id);			
		$this->type="text";					
	}		
	public static function factory($id) {			
		return new CDialog($id);		
	}		
	public function set_title($title) {			
		$this->title = $title;			
		return $this;		
	}		
	public function html($indent=0) {			
		$html = new CStringBuilder();			
		$html->set_indent($indent);			
		$html->appendln('<div id="'.$this->id.'" class="modal hide">')->inc_indent()->br();			
		$html->appendln('<button data-dismiss="modal" class="close" type="button">x</button>')->br();			
		$html->appendln('<div class="modal-header">')->inc_indent()->br();			
		$html->appendln('<h3>'.$this->title.'</h3>')->br();			
		$html->dec_indent()->appendln('</div>');			
		$html->appendln('<div class="modal-body">')->inc_indent()->br();			
		$html->appendln(parent::html($html->get_indent()))->br();			
		$html->dec_indent()->appendln('</div>');			
		$html->dec_indent()->appendln('</div>');						
		return $html->text();
	}		
	public function js($indent=0) {			
		return "";		
	}	
}