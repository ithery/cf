<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Slick extends CMobile_Element_AbstractComponent {

	protected $options;

    public function __construct($id = "") {
        parent::__construct($id);
        $this->options = array();
        $this->tag = "div";
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Slick($id);
    }
	
	public function set_options($options) {
		if(is_array($options)) {
			$this->options = array_merge($this->options, $options);
		}
		return $this;
	}
	public function add_item($id="") {
		$element = CMobile_Element_Component_Slick_Item::factory($id);
		$this->add($element);
		return $element;
	}
	
	public function js($indent=0) {
		$options = "";
		if(count($this->options) > 0) {
			foreach ($this->options as $key => $value) {
				if(strlen($options) > 0) {
					$options .= ", ";
				}
				$options .= $key . ': ' . $value;
			}
		}
		$js = "
		var wto" . $this->id . ";
		
		$('#".$this->id."').slick({
		  " . $options . "
		});
			
		";
		$js.= parent::js();
		return $js;
	}
}
