<?php

class cphp {

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
            if(!file_exists($filename)){
                throw new Exception($filename." Not found");
            }
            return include $filename;
	}
	
	
}