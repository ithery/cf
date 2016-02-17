<?php

	class CPage_Header extends CElement {
		private static $instance;
		
		public function __construct() {
			
		}
		public static function instance() {
			if(self::$instance==null) {
				self::$instance = new CPage_Header();
			}
			return self::$instance;
		}
	}
?>