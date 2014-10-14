<?php
class cvariable {
	public static function get($key,$user_id="") {
		$db = CDatabase::instance();
		$app = CApp::instance();
		$org = $app->org();
		$org_id = "";
		if($org!=null) {
			$org_id=$org->org_id;
		}
		$ret = null;
		if (strlen($user_id)>0) {
			$r=$db->query("
				select 
					value 
				from 
					var_user 
				where 
					status>0
					and org_id=".$db->escape($org_id)."
					and ".$db->escape_column('key')."=".$db->escape($key)." 
					and user_id=".$db->escape($user_id)."
					
			");
			if ($r->count()>0) $ret = $r[0]->value;
		}
		if ($ret==null) {
			if(strlen($user_id)>0) {
				$r=$db->query("
					select 
						value 
					from 
						var 
					where 
						status>0
						and org_id=".$db->escape($org_id)."
						and ".$db->escape_column('key')."=".$db->escape($key)." 
						and is_var_user=1
				");
			} else {
				$r=$db->query("
					select 
						value 
					from 
						var 
					where 
						status>0
						and org_id=".$db->escape($org_id)."
						and ".$db->escape_column('key')."=".$db->escape($key)."
				");
			}
			if ($r->count()>0) $ret = $r[0]->value;
		}
		return $ret;
	}
	public static function get_var_user($key,$user_id) {
		$db = CDatabase::instance();
		$app = CApp::instance();
		$org = $app->org();
		$org_id = "";
		if($org!=null) {
			$org_id=$org->org_id;
		}
		$ret = null;
		if (strlen($user_id)>0) {
			$r=$db->query("
				select 
					value 
				from 
					var_user 
				where 
					status>0
					and org_id=".$db->escape($org_id)."
					and ".$db->escape_column('key')."=".$db->escape($key)." 
					and user_id=".$db->escape($user_id)."
			");
			if ($r->count()>0) $ret = $r[0]->value;
		}
		return $ret;
	}
	public static function set($key, $value,$user_id="") {
		$db = CDatabase::instance();
		$app = CApp::instance();
		$org = $app->org();
		$org_id = "";
		if($org!=null) {
			$org_id=$org->org_id;
		}
		
		if(strlen($user_id)>0) {
			$var = cvariable::get_var_user($key,$user_id);
		} else {
			$var = cvariable::get_var($key,$user_id);
		}
		
		$table = "var";
		$data = array("value"=>$value);
		if (strlen($user_id)>0){
			$table = "var_user";
		}
		if (!is_null($var)) {
			$data =  $data = array_merge($data,array("updated"=>date("Y-m-d H:i:s")));

			$where = array("org_id"=>$org_id,"key"=>$key);
			if (strlen($user_id)>0){
				$where = $where = array_merge($where,array("user_id"=>$user_id));
			}
			$db->update($table,$data,$where);
		} else {
			$data = $data = array_merge($data,array("created"=>date("Y-m-d H:i:s")));
			$data = $data = array_merge($data,array("org_id"=>$org_id,"key"=>$key));
		
			if (strlen($user_id)>0){
				$data = $data = array_merge($data,array("user_id"=>$user_id));
			}
			$db->insert($table,$data);
		}
	}


}
?>
