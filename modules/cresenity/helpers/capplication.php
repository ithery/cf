<?php defined('SYSPATH') OR die('No direct access allowed.');

class capplication {
	public static function css($name) {
		return '<link href="'.curl::base().'.cresenity/css/?n=bootstrap" rel="stylesheet">';
	}
	
	public static function activation_link($org_id) {
		$org = corg::get($org_id);
		return curl::base(false,"http")."cresenity/activation/".$org->activation_code;
	}
	
}