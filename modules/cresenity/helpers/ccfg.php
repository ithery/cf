<?php

class ccfg {

    protected static $_config = array();
	
	public static function get_data($name,$app_code=null) {
		if($app_code==null) {
			$app = CApp::instance();
			$app_code=$app->code();
		}
		$name = str_replace(".",DS,$name);
		
		
		$file = DOCROOT.'config'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.$app_code.DIRECTORY_SEPARATOR.$name.EXT;
		$ret = null;
		if(file_exists($file)) {
			$ret = include($file);
		}
		return $ret;
	}
	
    public static function get($key, $domain = "") {
        $val = null;
		
        $org_code = null;
		$org_id = null;
		if (cstg::get("domain")) $domain = cstg::get("domain");
		if(strlen($domain)==0) {
			$domain = crouter::domain();
			
			
		} 
		
		$data = CFData::get($domain, 'domain');
		if ($data != null) {
			$app_id = isset($data['app_id'])?$data['app_id']:null;
			$app_code = isset($data['app_code'])?$data['app_code']:null;
			$org_id = isset($data['org_id'])?$data['org_id']:null;
			$org_code = isset($data['org_code'])?$data['org_code']:null;
			$store_id = isset($data['store_id'])?$data['store_id']:null;
			$store_code = isset($data['store_code'])?$data['store_code']:null;
			
		   
		}
		
		if (!isset(self::$_config[$domain])) {
            $config_path = DOCROOT . "config" . DIRECTORY_SEPARATOR;
            $cresenity_config_file = $config_path . "app" . EXT;
            $config_file = $cresenity_config_file;
            if ($org_code != "") {
				
                $org_config_path = $config_path . "app" . DIRECTORY_SEPARATOR;
                if (!is_dir($org_config_path))
                    mkdir($org_config_path);
                $org_config_path = $org_config_path . $org_code . DIRECTORY_SEPARATOR;
                if (!is_dir($org_config_path))
                    mkdir($org_config_path);
                $org_config_path = $org_config_path . $domain . DIRECTORY_SEPARATOR;
                if (!is_dir($org_config_path))
                    mkdir($org_config_path);
                $org_config_file = $org_config_path . "app" . EXT;

                $config_file = $org_config_file;
            }
			
            if (!file_exists($config_file))
                $config_file = $cresenity_config_file;
			
            $ccfg = require $config_file;
			if(!is_array($ccfg)) {
				$ccfg = $config;
				unset($config);
			}
			
            $app_files = CF::get_files('config','app',$domain);
			$app_files = array_reverse($app_files);
			foreach ($app_files as $file) {
				$app_ccfg = include $file;
				if(!is_array($app_ccfg)) {
					trigger_error("Invalid Config Format On ".$file);
				}
				$ccfg = array_merge($ccfg,$app_ccfg);
			}
			
			
			self::$_config[$domain] = $ccfg;
			
        }
		
		
        if (isset(self::$_config[$domain][$key])) {
            $val = self::$_config[$domain][$key];
        }
		
        return $val;
    }

}

?>