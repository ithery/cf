<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Card_Content extends CMobile_Element_Component_AbstractCard {

	protected $img;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Card_Content($id);
    }
	
	
	public function build() {
		
		  
		$this->add_class('card-content');
		
	
	}
	
	


   

}
