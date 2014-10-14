<?php
class cstore {
	public static function get($store_type_id) {
		$db = CJDB::instance();
		$result = $db->get("store_type",array("store_type_id"=>$store_type_id));
		
  		$value = null;
		if ($result->count() > 0) 
			$value = $result[0];
		return $value;
	}

	
}