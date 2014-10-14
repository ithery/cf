<?php defined('SYSPATH') OR die('No direct access allowed.');

class CConfig {
	private static $config_base_path;
	private static $instances = array();
	private $filename;
	private $default_config = null;
	private $config = array();
	public function __construct($name) {
		$this->config_base_path = MODPATH."cresenity".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR;
		$path = $this->config_base_path;
		$config_name_array = explode(".",$name);
		$i=0;
		foreach($config_name_array as $cna) {
			$i++;
			$path.=$cna.DIRECTORY_SEPARATOR;
			if($i<count($config_name_array)) {
				if(!is_dir($path)) mkdir($path);
			}
		}
		$path = rtrim($path,DIRECTORY_SEPARATOR);
		$filename=$path.EXT;
		if(!file_exists($filename)) {
			file_put_contents($filename,'');
		}
		$this->filename = $filename;
		$config=array();
		require_once($this->filename);
		$this->config = $config;
		unset($config);
		
	}
	
	public static function & instance($name='cresenity') {
		if ( ! isset(CConfig::$instances[$name])) {
			// Create a new instance
			CConfig::$instances[$name] = new CConfig($name);
		}

		return CConfig::$instances[$name];
	}

	
	public function get($key,$safe=true) {
		if($safe) {
			if(!isset($this->config[$key])) return '';
		}
		
		return $this->config[$key];
	}
	
	public function config() {
		return $this->config;
	}
	
	public function filename() {
		return $this->filename;
	}
	
	public function set($key,$value) {
		$this->config[$key] = $value;
		return $this;
	}
	
	public function save($config=null) {
		if($config!=null) $this->config = $config;
		$php_value = $this->to_php_value($this->config);
		$text = '<?php'.PHP_EOL.'$config = '.$php_value.";";
		file_put_contents($this->filename,$text);
		return $this;
		
	}
	
	private function to_php_value($value,$indent=0) {
		if (is_string($value)) {
			return  '\''.str_replace('\'','\\\'',$value).'\'';
		}
		if (is_bool($value)) {
			return $value ? 'TRUE':'FALSE';
		}
		if (is_null($value)) {
			return 'NULL';
		}
		if (is_array($value)) {
			
			$temp = 'array('.PHP_EOL;
			$indent_text=str_repeat("\t",$indent);
			$indent_text_2=str_repeat("\t",$indent+1);
			foreach($value as $k=>$v) {
				$temp.=$indent_text_2.'\''.$k.'\' => ';
				$temp.=$this->to_php_value($v,$indent+1);
				$temp.=','.PHP_EOL;
			}
			$temp.=$indent_text.')';
			return $temp;
		}
		return ''.$value;
	}
}