<?php

require_once dirname(__FILE__).DS."Lib".DS."jsmin".DS."JSMin.class.php";
require_once dirname(__FILE__).DS."Lib".DS."phppacker".DS."class.JavaScriptPacker.php";

class CJSMin {
	private static $instance;
	public static function minify($js) {
		$js =  JSMin::minify($js);
		//$js = self::pack($js);
		return $js;
		
	}
	public static function pack($js) {
		$packer = new JavaScriptPacker($js,62, false, true);
		return $packer->pack();
	}
	
}