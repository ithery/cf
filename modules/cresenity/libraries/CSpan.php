<?php defined('SYSPATH') OR die('No direct access allowed.'); 

class CSpan extends CObservable {
	
	
	protected $col;
	public function __construct($id="") {
		
		parent::__construct($id);
		
		
		$this->col = 12;
		
	}
	
	public static function factory($id="") {
		return new CSpan($id);
	}
	
	public function set_col($col) {
	
		$this->col = $col;
		return $this;
	}
	

	public function html($indent=0) {
		$html = new CStringBuilder();
			$html->set_indent($indent);
			$disabled = "";
			$html->appendln('<div class="span'.$this->col.'">');
			
			
			$html->appendln(parent::html($html->get_indent()))->br();
			$html->appendln('</div>');
			
			return $html->text();	
	
		
	}
	public function js($indent=0) {
		return parent::js($indent);
	}
	
}

