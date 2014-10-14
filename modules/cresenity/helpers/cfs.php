<?php defined('SYSPATH') OR die('No direct access allowed.');
class cfs {
	function list_files($dir) {
		$result = array();
		$dir = rtrim($dir,DS).DS;
		if(is_dir($dir)) {  
			if($handle = opendir($dir)) {  
				while(($file = readdir($handle)) !== false) {  
					if($file == "." || $file == ".." || $file == "Thumbs.db") continue;
					if(is_dir($dir.$file)) continue;
					$result[]=$dir.$file;  
					
						
				}  
				closedir($handle);  
			}  
		}  
		return $result;
	}  
	function list_dir($dir) {
		$result = array();
		$dir = trim($dir,DS).DS;
		if(is_dir($dir)) {  
			if($handle = opendir($dir)) {  
				while(($file = readdir($handle)) !== false) {  
					if($file == "." || $file == ".." || $file == "Thumbs.db") continue;
					if(!is_dir($dir.$file)) continue;
					$result[]=$dir.$file;  
					
						
				}  
				closedir($handle);  
			}  
		}  
		return $result;
	}
	function delete_dir($dir, $virtual = false) {
		
		$ds = DIRECTORY_SEPARATOR;  
		$dir = $virtual ? realpath($dir) : $dir;  
		$dir = substr($dir, -1) == $ds ? substr($dir, 0, -1) : $dir;  
		if (is_dir($dir) && $handle = opendir($dir))  
		{  
			while ($file = readdir($handle))  
			{  
				if ($file == '.' || $file == '..')  
				{  
					continue;  
				}  
				elseif (is_dir($dir.$ds.$file))  
				{  
					delete_dir($dir.$ds.$file);  
				}  
				else  
				{  
					unlink($dir.$ds.$file);  
				}  
			}  
			closedir($handle);  
			rmdir($dir);  
			return true;  
		}  
		else  
		{  
			return false;  
		}  
	}  
	
	
	public function basename($str) {
		return basename($str);
	}
	
	
	public function mkdir($dir) {
		return mkdir($dir);
	}
	
	public function is_dir($dir) {
		return is_dir($dir);
	}
	
	public function mtime($file) {
		return filemtime($file);
	}
	public function mtime_diff($file,$time=null) {
		if($time==null) {
			return time()-cfs::mtime($file);
		}
		if(is_string($time)) $time = strtotime($time);
		return $time-cfs::mtime($file);
	}

}