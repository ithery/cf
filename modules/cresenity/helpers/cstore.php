<?php
class cstore {
	public static function get($org_code,$store_code) {
		$default_data = array(
			"store_id"=>"",
			"code"=>"",
			"name"=>"",
			
		);
		
		
		
		$data = cdata::get($store_code,$org_code.'/'.$store_code);
		
		if($data==null) return null;
		foreach($default_data as $k=>$v) {
			if(!isset($data[$k])) {
				$data[$k]=$v;
			}
		}
		//$data = array_merge($default_data,$data);
		
		if($data!=null) {
			$data = carr::to_object($data);
		}
		return $data;
		
		
	}
	/*
	public static function get($org_id,$store_id) {
		$db = CJDB::instance();
		$result = $db->get("store",array("org_id"=>$org_id,"store_id"=>$store_id));
		
  		$value = null;
		if ($result->count() > 0) 
			$value = $result[0];
		return $value;
	}
	*/
	public static function generate_api_key($org_id) {
		$db = CDatabase::instance();
		$str=date('Y-m-d h:i:s').$org_id;
		$key=md5($str);
		return $key;
	}
}