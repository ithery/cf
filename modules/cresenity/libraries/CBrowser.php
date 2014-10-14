<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Cresenity PHP Library.
 * @author     Hery Kurniawan
 */
require_once dirname(__FILE__)."/Lib/browscap/Browscap.php";

class CBrowser {
	
	private $browscap;
	protected static $_instance;
	public function __construct() {
		$cache_dir = MODPATH.'cresenity/cache/browscap';
		if(!is_dir($cache_dir)) mkdir($cache_dir);
		$this->browscap =  new Browscap($cache_dir);
	}
	
	public static function instance() {
		if(self::$_instance == null) self::$_instance = new CBrowser();
		return self::$_instance;
	}
	
	public function browser() {
		return $this->browscap->getBrowser();
	}
	
}