<?php
class cexport {
	public static function get_directory() {
		$path = DOCROOT."export".DIRECTORY_SEPARATOR;
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
		$path = cexport::get_directory();
		$path = cexport::makefolder($path,$folder);
		$basefile = basename($filename);
		for($i=0;$i<$depth;$i++) {
			$c = "_";
			if(strlen($basefile)>($i+1)) {
				$c = substr($basefile,$i,1);
				if(strlen($c)==0) $c = "_";
				$path = cexport::makefolder($path,$c);
			}
		}
		
		return $path.$filename;
	}
	
	public static function geturl($folder,$filename) {
		$depth = 5;
		$url = curl::base()."export/".$folder."/".
		$basefile = basename($filename);
		for($i=0;$i<$depth;$i++) {
			$c = "_";
			if(strlen($basefile)>($i+1)) {
				$c = substr($basefile,$i,1);
				if(strlen($c)==0) $c = "_";
				$url.=$c."/";
				
			}
		}
		
		return $url.$filename;
	}
}
