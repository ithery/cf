<?php
defined('SYSPATH') or die('No direct access allowed.');
require_once dirname(__FILE__) . "/Lib/jnlp/Jnlp.php";


class CJnlp extends Jnlp{
	
	public static function factory() {
		return new CJnlp();
	}
	
	
}
