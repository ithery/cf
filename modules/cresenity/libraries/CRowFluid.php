<?php defined('SYSPATH') OR die('No direct access allowed.'); 

class CRowFluid extends CObservable {
	
	
	
	public function __construct($id) {
		parent::__construct($id);
		
		
	}
	
	public static function factory($id) {
		return new CRowFluid($id);
	}
	


	public function html($indent=0) {
		$html = new CStringBuilder();
		$html->set_indent($indent);
		$disabled = "";
		$html->appendln('<div id="'.$this->id.'" class="row-fluid">');
		
		
		$html->appendln(parent::html())->br();
			$html->appendln('</div>');
		
		return $html->text();	
	
		
	}
	public function js($indent=0) {
		return parent::js($indent);
	}
	
}

