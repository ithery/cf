<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Nav_Item extends CMobile_Element_AbstractComponent {

	protected $img;
	protected $slide;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "li";
		$this->img = array();
		$this->slide = false;
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Nav_Item($id);
    }
	
	// public function build() {
	// }

}
