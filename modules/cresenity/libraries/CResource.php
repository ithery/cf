<?php

class CResource {
	
	public static $instances = array();
	
	private $type;
	private $content;
	private $minify;
	private function __construct($type) {
		$this->type = $type;
		$this->minify = true;
	}
	
	public static function & instance($type) {
        if(!in_array($type,array("js","css","img","font"))) {
			trigger_error("Invalid Media Type (".$type.")");
		}
		if (!isset(CResource::$instances[$type])) {
			CResource::$instances[$type]= new CResource($type);
		}
		

        return CResource::$instances[$type];
    }
	
	public function path() {
		return DOCROOT."media".DS.$this->type.DS;
	}
	
	public function create_hash($files) {
		if(!is_array($files)) $files=array($files);
		$hash = md5($this->type."_".serialize($files));
		
		$info_file = ctemp::makepath('resource_info',$hash);
		//create info file if not available
		
		if(!file_exists($info_file)) {
			file_put_contents($info_file,serialize($files));
			
		}
		
		$resource_file = ctemp::makepath('resource',$hash);
		//recreate file resource when mtime>from $files
		$recreate=false;
		if(file_exists($resource_file)) {
			foreach($files as $f) {
				if(cfs::mtime($this->path().$f)>cfs::mtime($resource_file)) {
					$recreate=true;
					break;
				}
			}
		} else {
			$recreate=true;
		}
		if($recreate) {
			//we create file resource
			$content = "";
			foreach($files as $f) {
				$content .= file_get_contents($this->path().$f);
				if($this->type=="js") $content.=PHP_EOL;
			}
			file_put_contents($resource_file,$content);
		}
		return $hash;
	}
	
	public function url($files) {
		return curl::base()."ccore/".$this->type."/".$this->create_hash($files);
	}
	
	
	
	
	public function load($hash) {
		$info_file = ctemp::makepath('resource_info',$hash);
		$resource_file = ctemp::makepath('resource',$hash);
		$content = '';
		if(file_exists($resource_file)) {
			$content = file_get_contents($resource_file);
			if($this->type=="css") {
				$content = str_replace('/media',curl::base()."media",$content);
			}
		}
		return $content;
	}
	
}