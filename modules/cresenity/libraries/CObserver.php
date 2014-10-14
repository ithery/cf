<?php
class CObserver {
	private static $_instance;
	private $obj_list;
	private $autoid;
	private function __construct() {
		
		$this->obj_list=array();
		$this->autoid=0;
	}
	
	public static function instance() {
		if(self::$_instance==null) {
			self::$_instance=new CObserver();
		}
		return self::$_instance;
	}
	
	public static function objects() {
		return $this->obj_list;
	}
	
	public function new_id() {
		//$this->autoid++;
		//return md5("capp_autoid_".$this->autoid.date("YmdHis"));
		return uniqid();
	}
	
    public function add(CObject $obj) {
		if (array_key_exists($obj->id(),$this->obj_list)) {
			trigger_error("Object '".$obj->id()."' is exists.", E_USER_WARNING);
		}
		
		
		$this->obj_list[$obj->id()]=$obj;
		
	}
	
}

