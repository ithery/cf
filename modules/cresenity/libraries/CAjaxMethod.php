<?php
	class CAjaxMethod {
		
		public $name = "";
		public $method = "GET";
		
		public $data = array();
		public $type = "";
		public $target = "";
		public $param = array();
		
		public function __construct() {
			
		}
		public static function factory() {
			return new CAjaxMethod();
		}
		
		public function set_data($key,$data) {
			$this->data[$key]=$data;
			return $this;
		}
		
		public function set_type($type) {
			$this->type = $type;
			return $this;
		}
		
		public function set_method($method) {
			$this->method = $method;
			return $this;
		}
		

		
		public function makeurl($indent=0) {
			$js = CStringBuilder::factory()->set_indent($indent);
			//generate ajax_method
			//save this object to file.
			$json = json_encode($this);
			
			$ajax_method = date('Ymd').cutils::randmd5();
			$file = ctemp::makepath("ajax",$ajax_method.".tmp");
			file_put_contents($file,$json);
			$base_url = curl::base();
			if(CApp::instance()->is_mobile()) {
				$base_url = curl::base(false, 'http');
			}
			return $base_url."ccore/ajax/".$ajax_method;
			
		}
	}
?>