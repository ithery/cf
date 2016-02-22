<?php

    class CMobile_PseudoElement extends CMobile_Element {

      

		public static function factory($id = "", $tag = "div") {
            return new CMobile_PseudoElement($id, $tag);
        }
		
        public function html($indent = 0) {
			return parent::html_child();
		}
		
		public function js($indent = 0) {
			return parent::js_child();
		}
		
		
        
    }

?>