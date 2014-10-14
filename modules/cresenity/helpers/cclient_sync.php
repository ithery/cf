<?php

class cclient_sync {
	
	public static function get_unsync_count($table,$org_id="",$store_id="") {
		$db = CDatabase::instance();
		$app = CApp::instance();
		$data = csync::data();
		$tables = array();
		if(strlen($org_id)==0) $org_id = $app->org()->org_id;
		if(strlen($store_id)==0) $store_id = $app->store()->store_id;
		
			
		$q = "select count(1) total from ".$db->escape_table($table)." where sync_status=0 and org_id=".$db->escape($org_id)." and store_id=".$db->escape($store_id);
		
		return cdbutils::get_value($q); 
		
	
	
	
		
	}
	public static function get_module_array() {
		$data = csync::data();
		$modules = array();
		foreach($data as $k=>$v) {
			$modules[]=$k;
		}
		return $modules;
		
	}
	
	
	
	
}