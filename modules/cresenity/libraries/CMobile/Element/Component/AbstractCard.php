<?php

defined('SYSPATH') OR die('No direct access allowed.');

abstract class CMobile_Element_Component_AbstractCard extends CMobile_Element_AbstractComponent {

	protected $img;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
    }

    public function add_card_content($id="") {
		$element = CMobile_Element_Component_Card_Content::factory($id);
		$this->add($element);
		return $element;
		
	}

    public function add_card_title($id="") {
		$element = CMobile_Element_Component_Card_Title::factory($id);
		$this->add($element);
		return $element;
		
	}

    public function add_card_image($id="") {
		$element = CMobile_Element_Component_Card_Image::factory($id);
		$this->add($element);
		return $element;
	}
	
	public function add_card_action($id="") {
		$element = CMobile_Element_Component_Card_Action::factory($id);
		$this->add($element);
		return $element;
	}
	
	
   

}
