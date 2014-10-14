<?php
class ctemp {
	public static function get_directory() {
		$path = DOCROOT."temp".DIRECTORY_SEPARATOR;
		if(!is_dir($path)) {
			mkdir($path);
		}
		
		return $path;		
	}
	public static function makedir($path) {
		if(!is_dir($path)) {
			mkdir($path);
		}
		
		return $path;
	}
	public static function makefolder($path,$folder) {
		$path = $path.$folder.DIRECTORY_SEPARATOR;
		if(!is_dir($path)) {
			mkdir($path);
		}
		
		return $path;
	}
	public static function makepath($folder,$filename) {
		$depth = 5;
		$path = ctemp::get_directory();
		$path = ctemp::makefolder($path,$folder);
		$basefile = basename($filename);
		for($i=0;$i<$depth;$i++) {
			$c = "_";
			if(strlen($basefile)>($i+1)) {
				$c = substr($basefile,$i,1);
				if(strlen($c)==0) $c = "_";
				$path = ctemp::makefolder($path,$c);
			}
		}
		
		return $path.$filename;
	}
	
}
