<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Video extends CMobile_Element {

	protected $control;
    public function __construct($id = "") {
        parent::__construct($id);
        $this->tag = "video";
        $this->control = true;
    }

    public static function factory($id = "") {
        return new CMobile_Element_Video($id);
    }

    public function set_control($value) {
		$this->control = $value;
		return $this;
	}
	
	public function build() {
		if($this->control) {
			$this->set_attr('controls', '');
		}
	}
}
