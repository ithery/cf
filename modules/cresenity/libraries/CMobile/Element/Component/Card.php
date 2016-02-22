<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Card extends CMobile_Element_Component_AbstractCard {

	protected $img;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Card($id);
    }
	
	
	public function build() {
		$this->add_class('card');
	}
	
	
	
	public function js($indent=0) {
		
		$js = "
			//alert('a');
			//$('#".$this->id."').carousel();
			
		";
		return $js;
	}


   

}
