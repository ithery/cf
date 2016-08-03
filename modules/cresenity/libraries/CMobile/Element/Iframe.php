<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Iframe extends CMobile_Element {

	protected $src = "";
    public function __construct($id = "") {
        parent::__construct($id);
        $this->tag = "iframe";
    }

    public static function factory($id = "") {
        return new CMobile_Element_Iframe($id);
    }

    public function set_src($src) {
		$this->src = $src;
		return $this;
	}
	
	public function build() {
		$this->set_attr('src',$this->src);
	}
}
