<?php defined('SYSPATH') OR die('No direct access allowed.');

class CClientScript extends CObject {
	protected $scripts;
	protected static $_instance;
	
	
	public function __construct() {
		$this->reset();
	}
	
	//position
	//head, begin, end, load, ready
	//type
	//js_file,css_file, js, css, meta, link
	
	
	public static function instance() {
		if(self::$_instance==null) {
			self::$_instance = new CClientScript();
		}
		return self::$_instance;
	}
	
	public function reset() {
		$ALLPOS = array("head","begin","end","load","ready");
		$ALLTYPE = array("js_file","css_file","js","css","meta","link");
		$this->scripts = array();
		foreach($ALLPOS as $pos) {
			$this->scripts[$pos]=array();
			foreach($ALLTYPE as $type) {
				$this->scripts[$pos][$type]=array();
			}
		}
		
	}
	
	public function fullpath_js_file($file) {
		$dirs = CF::get_dirs('media');
		
		foreach($dirs as $dir) {
			$path = $dir.'js'.DS.$file;
			if(file_exists($path)) {
				return $path;
			}
		
		}
		
		$path = DOCROOT."media".DS.'js'.DS;
		return $path.$file;
	}
	public function fullpath_css_file($file) {
		$dirs = CF::get_dirs('media');
		
		foreach($dirs as $dir) {
			$path = $dir.'css'.DS.$file;
			if(file_exists($path)) {
				return $path;
			}
		
		}
		$path = DOCROOT."media".DS.'css'.DS;
		return $path.$file;
	}
	public function url_js_file($file=null) {
		if($file==null) {
			$files = $this->js_files();
			$urls = array();
			foreach($files as $f) {
				$urls[] = $this->url_js_file($f);
			}
			return $urls;
		}
		//return CResource::instance('js')->url($file);
		
		//$path = curl::base()."media/js/";
		$docroot=str_replace(DS,"/",DOCROOT);
		$file=str_replace(DS,"/",$file);
		$file=str_replace($docroot,curl::base(),$file);
		return $file;
	}
	public function url_css_file($file=null) {
		if($file==null) {
			$files = $this->css_files();
			
			$urls = array();
			foreach($files as $f) {
				$urls[] = $this->url_css_file($f);
			}
			return $urls;
		}
		//return CResource::instance('css')->url($file);
		$docroot=str_replace(DS,"/",DOCROOT);
		$file=str_replace(DS,"/",$file);
		$file=str_replace($docroot,curl::base(),$file);
		return $file;
	}
	
	public function register_js_files($files,$pos="end") {
		if(!is_array($files)) $files = array($files);
		foreach($files as $file) {
			$this->register_js_file($file,$pos);
		}
		
	}
	public function register_css_files($files,$pos="head") {
		if(!is_array($files)) $files = array($files);
		foreach($files as $file) {
			$this->register_css_file($file,$pos);
		}
		
	}
	public function register_js_file($file,$pos="end") {
		$js_file = $this->fullpath_js_file($file);
		if(!file_exists($js_file)) {
			trigger_error('JS File not exists, '.$file);
			
		}
		$this->scripts[$pos]['js_file'][] = $js_file;
	}
	public function register_css_file($file,$pos="head") {
		$css_file = $this->fullpath_css_file($file);
		if(!file_exists($css_file)) {
			trigger_error('CSS File not exists, '.$file);
			
		}
		$this->scripts[$pos]['css_file'][] = $css_file;
	}
	
	public function js_files() {
		$js_file_array = array();
		foreach($this->scripts as $sc) {
			
			foreach($sc['js_file'] as $k) {
				$js_file_array[]=$k;
			}
			
			
		}
		return $js_file_array;
	}
	
	public function create_js_hash() {
		return CResource::instance('js')->create_hash($this->js_files());
	}
	
	public function css_files() {
		$css_file_array = array();
		foreach($this->scripts as $sc) {
			
			foreach($sc['css_file'] as $k) {
				$css_file_array[]=$k;
			}
			
			
		}
		return $css_file_array;
	}
	
	public function create_css_hash() {
		return CResource::instance('css')->create_hash($this->css_files());
	}
	
	
	
	public function js($hash) {
		return CResource::instance('js')->load($hash);
		
		
	}
	
	
	public function css($hash) {
		return CResource::instance('css')->load($hash);
		
		
		
		
	}
	
	public function render_js_require($js) {
		//return CClientModules::instance()->require_js($js);
		$js_files = $this->js_files();
		$js_open = "";
		$js_close = "";
		$i=0;
		foreach($js_files as $f) {
			$js_open.=str_repeat("\t",$i)."require(['".$this->url_js_file($f)."'],function(){".PHP_EOL;
			
			$js_close.="})";
			$i++;
		}
		
		return $js_open.$js.PHP_EOL.$js_close.";".PHP_EOL;
	}
	
	
	public function render($pos,$type=array("js_file","css_file","js","css","meta","link")) {
		$script = "";
		
		if(!is_array($type)) $type = array($type);
		foreach($this->scripts[$pos] as $k=>$v) {
			if(in_array($k,$type)) {
				foreach($v as $s) {
					switch($k) {
						case "js_file":
							if(!ccfg::get('merge_js')) {
								$script.='<script src="'.$this->url_js_file($s).'"></script>'.PHP_EOL;
							}
						break;
					}
					switch($k) {
						case "css_file":
							if(!ccfg::get('merge_css')) {
								$script.='<link href="'.$this->url_css_file($s).'" rel="stylesheet" />'.PHP_EOL;
							}
						break;
					}
				}
			}
		}
		
		return $script;
	}
	
}