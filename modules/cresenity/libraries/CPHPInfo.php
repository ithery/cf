<?php

class CPHPInfo {
	private static $_instance = null;
	private $_info = array();
	public function __construct() {
		ob_start();
		@phpinfo();
		$phpinfo = array('phpinfo' => array());
		if(preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER))
			foreach($matches as $match)
				if(strlen($match[1]))
					$phpinfo[$match[1]] = array();
				elseif(isset($match[3]))
					$phpinfo[end(array_keys($phpinfo))][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
				else
					$phpinfo[end(array_keys($phpinfo))][] = $match[2];
		
		$this->_info = $phpinfo;
	}
	
	public static function instance() {
		if(self::$_instance==null) {
			self::$_instance = new CPHPInfo();
		}
		return self::$_instance;
	}
	
	public function get_array() {
		return $this->_info;
	}
	
	public function system() {
		if(isset($this->_info['phpinfo']['System'])) return $this->_info['phpinfo']['System'];
		return false;
	}
	
	public function server_api() {
		if(isset($this->_info['phpinfo']['Server API'])) return $this->_info['phpinfo']['Server API'];
		return false;
	}
	
	public function loaded_configuration_file() {
		if(isset($this->_info['phpinfo']['Loaded Configuration File'])) return $this->_info['phpinfo']['Loaded Configuration File'];
		return false;
	}
}

?>