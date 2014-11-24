<?php

class csync {
	public static function error_response($error_message,$format="json") {
		$result = array();
		$result['result']=0;
		$result['data']=array();
		$result['message']=$error_message;
		return cjson::encode($result);
	}
	
	public static function success_response($data,$format="json") {
		$result = array();
		$result['result']=1;
		$result['data']=$data;
		return cjson::encode($result);
	}
	
	public static function build_request($data,$api_key=null) {
		if($api_key==null) $api_key = '14a41ddfd1adbeee7ad213aa832638d7';
		$result = array();
		$result['api_key']=$api_key;
		$result['data']=$data;
		return cjson::encode($result);
	}
	
	public static function makepath($org_code,$session_id) {
		$path = DOCROOT."sync".DIRECTORY_SEPARATOR;
		if(!is_dir($path)) mkdir($path);
		$path = $path.$org_code.DIRECTORY_SEPARATOR;
		if(!is_dir($path)) mkdir($path);
		$date = substr($session_id,0,8);
		$hour = substr($session_id,8,2);
		$path = $path.$date.DIRECTORY_SEPARATOR;
		if(!is_dir($path)) mkdir($path);
		$path = $path.$hour.DIRECTORY_SEPARATOR;
		if(!is_dir($path)) mkdir($path);
		$path = $path.$session_id;
		return $path;
		
	}

    public static function data($app_code=null) {
        $data_path = DOCROOT."config".DIRECTORY_SEPARATOR."synchronize".DIRECTORY_SEPARATOR;
        $code = $app_code;
        if($code==null) {
            $app = CApp::instance();
            $code = $app->code();
        }
        $data_path.=$code.DIRECTORY_SEPARATOR."synchronize.php";
		$data =array();
		if(file_exists($data_path)) {
			$data = include $data_path;
		}
		
		$app_files = CF::get_files('config','synchronize');
		//$this->all_modules = include DOCROOT."config".DS."client_modules".DS."client_modules.php";
		$app_files = array_reverse($app_files);
		
		foreach ($app_files as $file) {
			$app_modules = include $file;
			if(!is_array($app_modules)) {
				trigger_error("Invalid Client Modules Config Format On ".$file);
			}
			
			$data = array_merge($data,$app_modules);
		}
		
        return $data;
    }
}