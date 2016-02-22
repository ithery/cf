<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Img extends CMobile_Element {


	protected $src = "";

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "img";
		$this->is_empty = true;
		$this->src = "";
    }

    public static function factory($id = "") {
        return new CMobile_Element_Img($id);
    }

	
	public function set_src($src) {
		$this->src = $src;
		return $this;
	}
	
	public function build() {
		$this->set_attr('src',$this->src);
	}
	
	

}
