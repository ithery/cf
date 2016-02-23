<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Chip extends CMobile_Element_AbstractComponent {

	protected $image;
	protected $image_alt;
	protected $text;
	protected $close_button;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
        $this->image = "";
        $this->image_alt = "";
        $this->text = "";
        $this->close_button = false;
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Chip($id);
    }


	public function set_image($image) {
		$this->image = $image;
		return $this;
	}

	public function set_alt_image($set_alt_image) {
		$this->image_alt = $set_alt_image;
		return $this;
	}

	public function set_text($text) {
		$this->text = $text;
		return $this;
	}

	public function set_close_button($close_button) {
		$this->close_button = $close_button;
		return $this;
	}

	public function build() {
		$this->add_class('chip');
		if(strlen($this->image)) {
			$this->add('<img src="' . $this->image . '" alt="' . $this->image_alt . '">');
		}
		$this->add($this->text);
		if($this->close_button) {
			$this->add('<i class="material-icons">close</i>');
		}
	}

	public function js($indent=0) {
		$js = "";
		return $js;
	}
}
