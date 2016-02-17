<?php

	class CPage_Footer extends CElement {
		
		private static $instance;
		
		public static function instance() {
			if(self::$instance==null) {
				self::$instance = new CPage_Footer();
			}
			return self::$instance;
		}
	}
?>