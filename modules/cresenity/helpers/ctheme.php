<?php
class ctheme {
	
	public static function path() {
		$theme = ctheme::gettheme();
		if(strlen($theme)==0) return "";
		return "themes"."/".ctheme::gettheme()."/";
	}
	
	
	public static function defaultlang() {
		return ccfg::get('theme');
	}
	public static function gettheme() {
		$session = Session::instance();
		$theme = $session->get("theme");
		if($theme==null) $theme=ctheme::defaultlang();
		return $theme;
	}
	public static function settheme($theme) {
		$session = Session::instance();
		$session->set("theme",$theme);
		
	}
	
}
