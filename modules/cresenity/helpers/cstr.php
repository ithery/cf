<?php
class cstr {
	public static function strip_thousand_separator($val) {
		$x=$val;
		$dec_sep = "";
		if(strlen($x)>3) {
			$dec_sep = substr($x,strlen($x)-3,1);
		}
		if($dec_sep==","||$dec_sep==".") {
			if($dec_sep==",") {
				$x = str_replace(".","",$x);
			} else {
				$x = str_replace(",","",$x);
			}
		} else {
			$x = str_replace(".","",$x);
			$x = str_replace(",","",$x);
		}
		return $x;
	}
	
	public static function replace_id_month($val) {
		$val_new = $val;
		$val_new = str_replace("Januari","January",$val_new);
		$val_new = str_replace("Februari","February",$val_new);
		$val_new = str_replace("Maret","March",$val_new);
		$val_new = str_replace("April","April",$val_new);
		$val_new = str_replace("Mei","May",$val_new);
		$val_new = str_replace("Juni","June",$val_new);
		$val_new = str_replace("Juli","July",$val_new);
		$val_new = str_replace("Agustus","August",$val_new);
		$val_new = str_replace("September","September",$val_new);
		$val_new = str_replace("Oktober","October",$val_new);
		$val_new = str_replace("November","November",$val_new);
		$val_new = str_replace("Desember","December",$val_new);
		
		$val_new = str_replace("Agust","Aug",$val_new);
		$val_new = str_replace("Agu","Aug",$val_new);
		$val_new = str_replace("Okt","Oct",$val_new);
		$val_new = str_replace("Des","Dec",$val_new);
		
		return $val_new;
	}
	

	public static function len($str) {
		return strlen($str);
	}
	
	public static function toupper($str) {
		return strtoupper($str);
	}
	public static function tolower($str) {
		return strtolower($str);
	}
	public static function pos($string,$needle,$offset=0) {
		return strpos($string,$needle,$offset);
	}
	
	
	public static function between($open,$close,$str) {
		$start_index = strpos($str,$open);
		$end_index = strpos($str,$close);
		$start_index += cstr::len($open);
		$str = substr($str,$start_index,$end_index-$start_index);
		return $str;
		
	}
	public static function sanitize($string = '', $is_filename = FALSE) {
		// Replace all weird characters with dashes
		$string = preg_replace('/[^\w\-'. ($is_filename ? '~_\.' : ''). ']+/u', '-', $string);

		// Only allow one dash separator at a time (and make string lowercase)
		return mb_strtolower(preg_replace('/--+/u', '-', $string), 'UTF-8');
	}
	
	public static function ellipsis($str,$length) {
		if((strlen($str)+3)>$length) $str = substr($str,0,$length)."...";
		return $str;
	}
	
	public static function between_replace ($open, $close, &$in, $with, $limit=false, $from=0) {
        if ($limit!==false && $limit==0)
        {
            return $in;
        }        
        $open_position = strpos ($in, $open, $from);
        if ($open_position===false)
        {
            return false;
        };
        $close_position = strpos ($in, $close, $open_position+strlen($open));
        if ($close_position===false)
        {
            return false;
        };
        $current = false;
        if (strpos($with,'{*}')!==false)
        {
            $current = substr ($in, $open_position+strlen($open), $close_position-$open_position-strlen($open));
            $current = str_replace ('{*}',$current,$with);
            //debug_echo ($current);
        }
        else
        {
            $current = $with;
        }
        $in = substr_replace ($in, $current, $open_position+strlen($open), $close_position-$open_position-strlen($open));
        $next_position = $open_position + strlen($current) + 1;
        if ($next_position>=strlen($in))
        {
            return false;
        }
        if ($limit!==false)
        {
            $limit--;
        }        
        between_replace ($open, $close, $in, $with, $limit, $next_position);
        return $in;
    }
	
	
	
}
