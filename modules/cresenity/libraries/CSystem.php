<?php defined('SYSPATH') OR die('No direct access allowed.'); 
require_once dirname(__FILE__)."/Lib/csysinfo/CSysInfo.php";

class CSystem extends CObject {
	
	
	protected static $_instance;
	protected $cache_lifetime;
	public function __construct($id="") {
		parent::__construct($id);

		$this->cache_lifetime = 24*60*60;
	}
	
	
	public static function instance($id="") {
		if(self::$_instance==null) self::$_instance=new CSystem($id);
		return self::$_instance;
	}
	
	public function hostname() {
		$hostname = CCache::instance()->get('csystem_hostname');
		if($hostname==null) {
			$hostname = CSysInfo::instance()->sys()->getHostname();
			CCache::instance()->set('csystem_hostname',$hostname,'csystem',$this->cache_lifetime);
		}
		return $hostname;
	}
	public function ip_address() {
		$ip_address = CCache::instance()->get('csystem_ip_address');
		if($ip_address==null) {
			$ip_address = CSysInfo::instance()->sys()->getIp();
			CCache::instance()->set('csystem_ip_address',$ip_address,'csystem',$this->cache_lifetime);
		}
		return $ip_address;
	}
	public function kernel() {
		$kernel = CCache::instance()->get('csystem_kernel');
		if($kernel==null) {
			$kernel = CSysInfo::instance()->sys()->getKernel();
			CCache::instance()->set('csystem_kernel',$kernel,'csystem',$this->cache_lifetime);
		}
		return $kernel;
	}
	public function distro() {
		$distro = CCache::instance()->get('csystem_distro');
		if($distro==null) {
			$distro = CSysInfo::instance()->sys()->getDistribution();
			CCache::instance()->set('csystem_distro',$distro,'csystem',$this->cache_lifetime);
		}
		return $distro;
	}
	public function distro_icon() {
		$distro_icon = CCache::instance()->get('csystem_distro_icon');
		if($distro_icon==null) {
			$distro_icon = CSysInfo::instance()->sys()->getDistributionIcon();
			CCache::instance()->set('csystem_distro_icon',$distro_icon,'csystem',$this->cache_lifetime);
		}
		return $distro_icon;
		
	}
	public function users() {
		$users = CCache::instance()->get('csystem_users');
		if($users==null) {
			$users = CSysInfo::instance()->sys()->getUsers();
			CCache::instance()->set('csystem_users',$users,'csystem',$this->cache_lifetime);
		}
		return $users;
	}
	public function load_avg() {
		return CSysInfo::instance()->sys()->getLoad();
	}
	public function cpu_load() {
		return CSysInfo::instance()->sys()->getLoadPercent();
	}
	
	
	
}

