<?php defined('SYSPATH') OR die('No direct access allowed.'); 

class CChartPie extends CElement {
	
	
	
	public function __construct($id="") {
		
		parent::__construct($id);
		

		
	}
	
	public static function factory($id="") {
		return new CChartPie($id);
	}
	


	public function html($indent=0) {
		$html = new CStringBuilder();
		$html->set_indent($indent);
		$disabled = "";
		$html->appendln('<span>');
		
		
		$html->appendln(parent::html($html->get_indent()))->br();
		$html->appendln('</span>');
		
		return $html->text();	
	
		
	}
	public function js($indent=0) {
		return parent::js($indent);
	}
	
}

