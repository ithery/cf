<?php
class cstg {
	
	protected static $_config=null;
	public static function get($key) {
		$val = null;
		
	
		
		if(self::$_config==null) {
			$config_path = DOCROOT."config"."/";
			$cresenity_config_file = $config_path."setting".EXT;
			$config_file = $cresenity_config_file;
			
			
			require $config_file;
			self::$_config = $config;
			unset($config);
		}
		if(isset(self::$_config[$key])) {
			$val = self::$_config[$key];
		}
		return $val;
	}
	
	
}

?>