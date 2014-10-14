<?php defined('SYSPATH') OR die('No direct access allowed.');

class crouter {
	
	public function domain() {
		return $_SERVER["SERVER_NAME"];
	}
	
	public function controller() {
		return CFRouter::$controller;
	}
	public function method() {
		return CFRouter::$method;
	}
	
	public function routed_uri() {
		return CFRouter::$routed_uri;
	}
	public function complete_uri() {
		return CFRouter::$complete_uri;
	}
	public function query_string() {
		return CFRouter::$query_string;
	}
	public function current_uri() {
		return CFRouter::$current_uri;
	}
	public function url_suffix() {
		return CFRouter::$url_suffix;
	}
	public function segments() {
		return CFRouter::$segments;
	}
	public function controller_path() {
		return CFRouter::$controller_path;
	}
	public function arguments() {
		return CFRouter::$arguments;
	}
} // End crouter