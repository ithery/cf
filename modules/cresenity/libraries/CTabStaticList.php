<?php

class CTabStaticList extends CElement{
	
	public $tabs;
	public $tab_position;
	public $active_tab;
	
	public function __construct($id) {
		parent::__construct($id);
		
		$this->tab_position = "left";
		$this->active_tab = "";
		$this->tabs = array();
	}
	public static function factory($id) {
		return new CTabStaticList($id);
	}
	public function add_tab($id) {
		if(strlen($this->active_tab)==0) $this->active_tab = $id;
		$tab = CTabStatic::factory($id);
		$this->tabs[]=$tab;
		return $tab;
	}
	public function active_tab($tab_id) {
		
	}
	
	public function html($indent=0) {
		
		$html = new CStringBuilder();
		$html->set_indent($indent);
		$add_class="";
		if($this->tab_position=="left") {
			$add_class.=" tabs-left";
		}
		$html->appendln('<div class="tabbable '.$add_class.'">');
		$html->appendln('<ul class="nav nav-tabs ">');
        foreach($this->tabs as $tab) {
			
			if($tab->id==$this->active_tab) {
				$tab->set_active(true);
			}
			$html->appendln($tab->header_html($html->get_indent()));
		}
		$html->appendln('</ul>');
		$html->appendln('<div class="tab-content">');
		foreach($this->tabs as $tab) {
			$html->appendln($tab->html($html->get_indent()));
		}
        $html->appendln('</div>');       
        $html->appendln('</div>');       
        ;
		
		return $html->text();

	
	
		
	}
	
	public function js($indent=0) {
		
		$js = new CStringBuilder();
		$js->set_indent($indent);
		foreach($this->tabs as $tab) {
			$js->appendln($tab->js($js->get_indent()));
		}
		
		return $js->text();
	
	}
	
}
?>