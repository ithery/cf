<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Collection extends CMobile_Element_AbstractComponent {

	protected $image;
	protected $image_alt;
	protected $close_button;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
        $this->image = "";
        $this->image_alt = "";
        $this->close_button = false;
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Collection($id);
    }


	public function add_item($id="") {
		$element = CMobile_Element_Component_Collection_Item::factory($id);
		$this->add($element);
		return $element;
	}

	public function build() {
		$this->add_class('collection');
		if(strlen($this->image)) {
			$this->add('<img src="' . $this->image . '" alt="' . $this->image_alt . '">');
		}
		if($this->close_button) {
			$this->add('<i class="material-icons">close</i>');
		}
	}

	public function js($indent=0) {
		$js = "";
		$js.=parent::js();
		return $js;
	}
}
