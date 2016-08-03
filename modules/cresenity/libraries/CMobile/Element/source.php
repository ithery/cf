	<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Source extends CMobile_Element {

	protected $src = "";
	protected $type = "";

    public function __construct($id = "") {
        parent::__construct($id);
        $this->tag = "source";
    }

    public static function factory($id = "") {
        return new CMobile_Element_Source($id);
    }

    public function set_src($src) {
		$this->src = $src;
		return $this;
	}

	public function set_type($type) {
		$this->type = $type;
		return $this;
	}

	public function build() {
		$this->set_attr('src',$this->src);
		$this->set_attr('type',$this->type);
	}
}
