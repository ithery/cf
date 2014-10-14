<?php defined('SYSPATH') OR die('No direct access allowed.'); 
require_once dirname(__FILE__)."/includes/config.php";	
class CSysInfo {
	
	private $_sysinfo;
	
	protected static $_instance;
	public function __construct() {
		 
		if (!file_exists(dirname(__FILE__).'/includes/os/class.'.PHP_OS.'.inc.php')) {
            trigger_error(PHP_OS." is not currently supported");
		}
		require_once dirname(__FILE__).'/includes/os/class.'.PHP_OS.'.inc.php';
		$os = PHP_OS;
        $this->_sysinfo = new $os();
	}
	
	
	public static function instance() {
		if(self::$_instance==null) self::$_instance=new CSysInfo();
		return self::$_instance;
	}
	
	public function sys() {
		return $this->_sysinfo->getSys();
 	}
	
	
}

