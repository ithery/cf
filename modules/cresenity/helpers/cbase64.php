<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * base64 helper class.
 */
class cbase64 {
	
	public static function encode($data){
		return base64_encode($data);
	}
	
	
	public static function decode( $data ) {
		return base64_decode($data);
	}
	
	function is_encoded() {
		if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
			return TRUE;
		} 
		return FALSE;
	}

	
} // End cbase64