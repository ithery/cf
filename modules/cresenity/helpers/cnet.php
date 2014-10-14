<?php defined('SYSPATH') OR die('No direct access allowed.');

class cnet {
	public static function ping($domain,$port=80) {
		$connected = @fsockopen($domain, $port); //website and port
		$is_conn = false;
		if ($connected){
			$is_conn = true; //action when connected
			fclose($connected);
		}
		return $is_conn;
	}

	public static function have_internet_connection() {
		return cnet::ping('www.cresenitytech.com');
	}
}