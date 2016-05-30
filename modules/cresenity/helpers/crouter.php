<?php defined('SYSPATH') OR die('No direct access allowed.');

class crouter {
	
	public static function domain() {
		return CF::domain();
	}
	
	public static function controller() {
		return CFRouter::$controller;
	}
	
	public static function controller_dir() {
		return CFRouter::$controller_dir;
	}
	
	public static function method() {
		return CFRouter::$method;
	}
	
	public static function routed_uri() {
		return CFRouter::$routed_uri;
	}
	public static function complete_uri() {
		return CFRouter::$complete_uri;
	}
	public static function query_string() {
		return CFRouter::$query_string;
	}
	public static function current_uri() {
		return CFRouter::$current_uri;
	}
	public static function url_suffix() {
		return CFRouter::$url_suffix;
	}
	public static function segments() {
		return CFRouter::$segments;
	}
	public static function controller_path() {
		return CFRouter::$controller_path;
	}
	public static function arguments() {
		return CFRouter::$arguments;
	}
} // End crouter