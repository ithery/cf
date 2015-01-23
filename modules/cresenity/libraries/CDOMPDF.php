<?php
defined('SYSPATH') or die('No direct access allowed.');
require_once dirname(__FILE__) . "/Lib/dompdf/dompdf_config.inc.php";


class CDOMPDF extends DOMPDF{
	
	public function __construct() {
		parent::__construct();
	}
	
	public static function factory() {
		return new CDOMPDF();
	}
	
	
}
