<?php defined('SYSPATH') OR die('No direct access allowed.');
class ctransform {
	public static function thousand_separator($rp,$decimal=null,$always_decimal=false) {
		$minus_str = "";
		
		if (strpos($rp,"-")!==false) {
			$minus_str = substr($rp,0,strpos($rp,"-")+1);
			$rp = substr($rp,strpos($rp,"-")+1);
		}
		$rupiah = "";
		$float = "";
		if (strpos($rp,".")>0) {
			$float = substr($rp,strpos($rp,"."));
			if(strlen($float)>3) {
				
				$char3 = $float[3];
				if($char3>=5) {
					$float[2] = $float[2]+1;
				} else {
					$float[2] = 0;
				}
				
			}
			
			$rp = substr($rp,0,strpos($rp,"."));
		}
		
		$p = strlen($rp);                    
		while($p > 3) {
			$rupiah = "," . substr($rp,-3) . $rupiah;
			$l = strlen($rp) - 3;
			$rp = substr($rp,0,$l);
			$p = strlen($rp);
		}
		$rupiah = $rp . $rupiah;
		if($decimal!=null) {
			if (strlen($float)>$decimal) $float = substr($float,0,$decimal+1);
		
		}
		if($always_decimal==false) {
			if($float==".00") $float = "";
		} 
		if(strlen($float)>3) {
			$float = substr($float,0,3);
		}
		return $minus_str.$rupiah.$float;
	}
	public static function short_date_format($x) {
		if(strlen($x)>10) $x = substr($x,0,10);
		return $x;
	}
	public static function uppercase($x) {
		return strtoupper($x);
	}
	public static function lowercase($x) {
		return strtolower($x);
	}
	public static function month_name($x) {
		return cutils::month_name($x);
	}
	public static function html_specialchars($x) {
		return html::specialchars($x);
	}
	
	public static function lang($x) {
		return clang::__($x);
	}
	public static function date_formatted($x) {
		if(strlen($x)==0) return $x;
		$date_format = ccfg::get('date_formatted');
                if(strlen($date_format)==0) return $x;
		return date($date_format,strtotime($x));
	}
	public static function long_date_formattted($x) {
		if(strlen($x)==0) return $x;
		$long_date_format = ccfg::get('long_date_formatted');
		if(strlen($long_date_format)==0) return $x;
		return date($long_date_format,strtotime($x));
	}
}