<?php
	class CAjaxParam {
		
		public $name = "";
		public $value = "";
		
		public $type = "value"; //value,input
		
		public function __construct($name) {
			$this->name = $name;
		}
		public static function factory($name) {
			return new CAjaxParam($name);
		}
		
		public function set_type($type) {
			$this->type = $type;
			return $this;
		}
		
		public function set_value($val) {
			$this->value = $val;
			return $this;
		}
		public function get_name() {
			return $this->name;
		}
		public function get_value() {
			return $this->value;
		}
		public function get_type() {
			return $this->type;
		}
	}
?>