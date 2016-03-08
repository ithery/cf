<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Nav_Brand extends CMobile_Element_AbstractComponent {

	protected $img;
	protected $slide;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "a";
		$this->img = array();
		$this->slide = false;
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Nav_Brand($id);
    }
	
	public function build() {
		$this->add_class('brand-logo');
	}

}
