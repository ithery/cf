<?php
defined('SYSPATH') or die('No direct access allowed.');
require_once dirname(__FILE__) . "/Lib/bar128/bar128.php";


class CBar128 extends Bar128 {
	
	public function __construct() {
		parent::__construct();
	}
	
	public static function factory() {
		return new CBar128();
	}
	
	
}
