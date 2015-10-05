<?php
class cnav_panel_dashboard {
	
	
	
	
	public static function have_access($nav=null,$role_id=null,$app_id=null,$domain=null) {
		
		$app = CApp::instance();
		if($role_id==null) {
			$role = $app->role();
			if($role!=null) $role_id = $role->role_id;
		}
		if($app_id==null) {
			$app_id = $app->app_id();
		}
		if($nav==null) $nav = cnav::nav();
		
		if($nav===false) return false;
		$db = CDatabase::instance($domain);
		if($role_id=="PUBLIC") {
			$role_id = null;
		}
		$role = cdbutils::get_row('select * from roles where role_id='.$db->escape($role_id));
		if($role->parent_id==null) return true;
		
		
		$q = "select * from role_nav_panel_dashboard where nav=".$db->escape($nav["name"])." and role_id=".$db->escape($role_id)." and app_id=".$db->escape($app_id);
		if($role_id==null) {
			$q = "select * from role_nav_panel_dashboard where nav=".$db->escape($nav["name"])." and role_id is null and app_id=".$db->escape($app_id);
		}
		
		$r = $db->query($q);
		return $r->count()>0;
	}
	public static function have_permission($action,$nav=null,$role_id=null,$app_id=null,$domain=null) {
		$app=CApp::instance();
		
        if($role_id==null) {
            $role = $app->role();
            if($role==null) return false;
            $role_id = $role->role_id;
        }
        if($app_id==null) {
            $app_id = $app->app_id();
        }
		$db = CDatabase::instance($domain);

		$role = cdbutils::get_row('select * from roles where role_id='.$db->escape($role_id));
		if($role->parent_id==null) return true;
		
		
        $db = CDatabase::instance($domain);
        $q = "select * from role_permission where name=".$db->escape($action)." and role_id=".$db->escape($role_id)." and app_id=".$db->escape($app_id);
        $r = $db->query($q);

        return $r->count()>0;


        /*
        if($nav==null) $nav = cnav::nav();
		
		if($nav===false) return false;
		
		$navname = $nav;
		if(is_array($navname)) {
			$navname = $nav["name"];
		}


		$q = "select * from role_permission where name=".$db->escape($action)." and nav=".$db->escape($navname)." and role_id=".$db->escape($role_id)." and app_id=".$db->escape($app_id);
		
		
		
		$r = $db->query($q);
		return $r->count()>0;
		*/
	}
	public static function app_user_rights_array($app_id,$role_id,$app_role_id="",$domain="") {
		$navs=CNavigationPanelDashboard::instance($app_id)->navs();
		return cnav_panel_dashboard::as_user_rights_array($app_id,$role_id,$navs,$app_role_id,$domain);
	}
	public static function as_user_rights_array($app_id,$role_id,$navs=null,$app_role_id="",$domain="",$level=0) {
		if($navs==null) $navs = CNavigationPanelDashboard::instance()->navs();
		
		
		
		$result = array();
		
		foreach($navs as $d) {
			if(!cnav_panel_dashboard::access_available($d,$app_id,$domain,$app_role_id)) {
				continue;
			}
				
			$res = $d;
			$res["level"]=$level;
			$res["role_id"]=$role_id;
			$res["app_id"]=$app_id;
			$res["domain"]=$domain;
			$result[]=$res;
			$result = array_merge($result);
		}
		return $result;
		
	}
	public static function is_public($nav) {
		if(isset($nav["is_public"])&&$nav["is_public"]) {
			return true;
		}
		return false;
	}
	
	
	public static function access_available($nav=null,$app_id="",$domain="",$app_role_id="") {
		if($nav==null) $nav = cnav::nav();
		if($nav===false) return false;
		$navname = $nav["name"];
		$app = CApp::instance();
		if(isset($nav["requirements"])) {
			$requirements = $nav["requirements"];
			foreach($requirements as $k=>$v) {
				
				$config_value = ccfg::get($k,$domain);
				if($config_value!=$v) {
					return false;
				}
			}
		}
		if(strlen($app_role_id)==0) {
			if($app->user()!=null) {
				$app_role_id = cobj::get($app->user(),'role_id');
			}
		}
		
		if(strlen($app_role_id)>0) {
			$app_role= crole::get($app_role_id);
			
				
			$parent_role_id = $app_role->parent_id;
			if($parent_role_id!=null) {
				if(!cnav_panel_dashboard::have_access($nav,$app_role_id,$app_id)) {
					
					return false;
				}
			}
			
		}
		
		return true;
	}
	
	public static function permission_available($action,$nav=null,$app_id="",$domain="",$app_role_id="") {

		if($nav==null) $nav = cnav_panel_dashboard::nav();
		if($nav===false) return false;
		
		if(!cnav_panel_dashboard::access_available($nav,$app_id,$domain,$app_role_id)) return false;
		
		$navname = $nav["name"];
		if(isset($nav["action"])) {
			$navactions = $nav["action"];
			foreach($navactions as $act) {
				if($act['name']==$action&&isset($act["requirements"])) {
				
					$requirements = $act["requirements"];
					
					foreach($requirements as $k=>$v) {
						$config_value = ccfg::get($k,$domain);
						if($config_value!=$v) {
							return false;
						}
					}
					
				}
			}
		
		}
	
		return true;
	}
	
} 

?>