<?php defined('SYSPATH') OR die('No direct access allowed.');

final class CFData {
	public static function path() {
		$dir = DOCROOT.'data'.DIRECTORY_SEPARATOR.'';
		return $dir;
	}
	
	public static function get($data_name,$folder="") {
		$file = self::path();
		if(strlen($folder)>0) {
			$folder=explode('/',$folder);
			foreach($folder as $row){
				if(strlen($row)==0){
					continue;
				}
				$file.=$row.DIRECTORY_SEPARATOR;
			}
		}
		$file.=$data_name;
		if(!file_exists($file.EXT)) return null;
		
		return self::load_value($file.EXT);
		//$content = file_get_contents($file);
		//return cjson::decode($content);
	}
	public static function set($data_name, $data,$folder="") {
		$file = self::path();
		if(strlen($folder)>0) {
			$folder=explode('/',$folder);
			foreach($folder as $row){
				if(strlen($row)==0){
					continue;
				}
				$file.=$row.DIRECTORY_SEPARATOR;
				if(!is_dir($file)) {
					mkdir($file);
				}
			}
		}
		$file.=$data_name.EXT;
		//$json = cjson::encode($data);
		self::save_value($data,$file);
		//file_put_contents($file,$json);
		return true;
	}
	
	public static function delete($data_name,$folder="") {
		$file = cdata::path();
		if(strlen($folder)>0) {
			$folder=explode('/',$folder);
			foreach($folder as $row){
				if(strlen($row)==0){
					continue;
				}
				$file.=$row.DIRECTORY_SEPARATOR;
				if(!is_dir($file)) {
					mkdir($file);
				}
			}
		}
		$file.=$data_name.EXT;
		if(file_exists($file)) {
			unlink($file);
		}
		
	}
	public static function string_value($val,$level=0) {
		
		$str = '';
		$eol = PHP_EOL;
		$indent = cutils::indent($level,"\t");
		if(is_array($val)) {
			$str.='array('.$eol;
			$indent2 = cutils::indent($level+1,"\t");
			foreach($val as $k=>$v) {
				
				$str.=$indent2."'".addslashes($k)."'=>";
				$str.=self::string_value($v,$level+1);
				$str.=",".$eol;
			}
			
			$str.=$indent.')';
		} else if(is_null($val)) {
			$str .= 'NULL';
		} else if(is_bool($val)) {
			
			$str .= ($val===TRUE?"TRUE":"FALSE");
		} else {
			$str .= "'".addslashes($val)."'";
		}
		return $str;
	}
	
	
	
	public static function save_value($value,$filename=null) {
		$val =  '<?php '.PHP_EOL.'return '.cphp::string_value($value).';';
		if($filename!=null) {
			file_put_contents($filename,$val);
		}
		return $val;
	}
	
	public static function load_value($filename) {
		return include $filename;
	}
}