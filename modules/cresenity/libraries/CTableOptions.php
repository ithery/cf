<?php
	class CTableOptions extends CList{

		private $default_options = array(
			"defer_render"=>true,
			"filter"=>true,
			"info"=>true,
			"length_change"=>true,
			"pagination"=>true,
			"height"=>false,
		);
		public function __construct() {
			parent::__construct();
			foreach($this->default_options as $k=>$v) {
				$this->add($k,$v);
			}
		}
		public static function factory() {
			return new CTableOptions();
		}
		
	
		
	}
?>