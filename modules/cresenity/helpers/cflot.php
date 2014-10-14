<?php defined('SYSPATH') OR die('No direct access allowed.');

class cflot {

	public static function list2piedata($list) {
		$result = array();
		foreach($list as $k=>$v) {
			$r = array();
			$r["label"]=$k;
			$r["data"]=$v;
			$result[]=$r;
		}
		return $result;
	}


}
