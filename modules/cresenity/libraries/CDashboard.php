<?php

class CDashboard extends CElement {
	protected $_dashboard=array();
    
	public function __construct($id='') {
		parent::__construct($id);
		$file = CF::get_file('data','dashboard');
		$dashboard=array();
		if(file_exists($file)) {
			$dashboard=include $file;
		}
		if(!is_array($dashboard)){
			$dashboard=array();
		}
		foreach($dashboard as $key=>$val){
			$this->_dashboard[$key]=$val;
		}
		
	}

	public static function factory($id=''){
        return new CDashboard($id);
	}
	
	public function html($indent=0){
        $html = new CStringBuilder();
		foreach ($this->_dashboard as $key=>$val){
			$div=$this->add_div();
			$label=carr::get($val,'label');
			$type=carr::get($val,'type');
			$options=carr::get($val,'options',array());
			$widget=$this->add_widget()->set_title($label);
			switch($type){
				case 'table' : 
				{
					$table=$widget->add_table();
					$table->set_title(carr::get($options,'title'));
					foreach(carr::get($options,'column',array()) as $c_key=>$v_key){
						$table->add_column($v_key)->set_label(strtoupper($v_key));
					}
					$table->set_data_from_query(carr::get($options,'query'));
					break;
				}
			}
			
		}
		$html->appendln(parent::html($indent));
        return $html->text();
	}

}

