<?php

class CDebug {
	private $data = null;
	private $render_type;
	public function __construct($var,$render_type='table') {
		$this->data = $var;
		$this->render_type = $render_type;
	}
	
	public function render() {
	
	}
}
