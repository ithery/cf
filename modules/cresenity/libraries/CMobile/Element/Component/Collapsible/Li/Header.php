<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_Collapsible_li_Header extends CMobile_Element_AbstractComponent {

    protected $icon;
    protected $text;
	protected $active;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
        $this->icon = '';
        $this->text = '';
        $this->active = false;
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_Collapsible_li_Header($id);
    }

	public function set_icon($icon) {
        $this->icon = $icon;
        return $this;
    }

    public function set_text($text) {
        $this->text = $text;
        return $this;
    }

    public function set_active($active) {
		$this->active = $active;
		return $this;
	}

	protected function html_attr() {
        $html_attr = parent::html_attr();
        return $html_attr;
    }

	public function build() {
		$html_attr = $this->html_attr();
		$this->add_class('collapsible-header');
        if($this->active) {
            $this->add_class('active');
        }
        if(strlen($this->icon) > 0) {
        	$this->add('<i class="material-icons">' . $this->icon . '</i>');
        }
        $this->add($this->text);
		
	}
}
