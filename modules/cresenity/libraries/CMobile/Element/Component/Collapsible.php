<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Collapsible extends CMobile_Element_AbstractComponent {

	protected $type;
	protected $popout;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
        $this->type = "accordion";
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Collapsible($id);
    }

	public function set_expandable($expandable) {
		$this->type = "expandable";
		return $this;
	}

	public function set_popout($popout) {
		$this->popout = $popout;
		return $this;
	}

	public function add_row($id="") {
		$element = CMobile_Element_Component_Collapsible_Li::factory($id);
		$this->add($element);
		return $element;
	}

	public function build() {
		$this->add_class('collapsible');
		$this->add_attr('data-collapsible', $this->type);
		if($this->popout) {
			$this->add_class('popout');
		}
	}

	public function js($indent=0) {
		$js = "";
		$js.=parent::js();
		return $js;
	}
}
