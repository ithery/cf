<?php defined('SYSPATH') OR die('No direct access allowed.');

class cparam {
	public static function get_params($cmd) {
		$params = array();
		$matches = array();
		preg_match_all("/{([\w]*)}/", $cmd, $matches, PREG_SET_ORDER);
		foreach ($matches as $val) {
			$str = $val[1]; //matches str without bracket {}
			$b_str = $val[0]; //matches str with bracket {}
			$params[]=$str;
		}
		return $params;
	}
	
	public static function have_param($cmd) {
		$params = cparam::get_params($cmd);
		return count($params)>0;
	}
	
	public static function fill_params($cmd,$params) {
		$new_str = $cmd;
		$matches = array();
		preg_match_all("/{([\w]*)}/", $cmd, $matches, PREG_SET_ORDER);
		foreach ($matches as $val) {
			$str = $val[1]; //matches str without bracket {}
			$b_str = $val[0]; //matches str with bracket {}
			if(isset($params[$str])) {
				$new_str = str_replace($b_str,$params[$str],$new_str);
			}
		}
		return $new_str;
	}
	
}