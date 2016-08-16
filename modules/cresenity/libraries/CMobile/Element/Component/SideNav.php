<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_SideNav extends CMobile_Element_AbstractComponent {

    protected $is_fixed = true;
    protected $button = null;
    protected $container = null;
    protected $menu_list = null;
    protected $manual;
    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
        $this->is_fixed = true;
        $this->manual = false;
        // $this->button = CMobile_Element_Component_SideNav_Button::factory();
        // $this->button->set_sidenav_id($this->id);
        // $this->button = $this->before()->add($this->button);
        $this->container = $this->add_div()->add_class('menu-container');
        $this->menu_list = $this->container->add_ul()->add_class('menu-list top-menu');
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_SideNav($id);
    }
    
    public function set_fixed($bool) {
        $this->is_fixed  = $bool;
        return $this;
    }

    public function set_manual($manual) {
        $this->manual  = $manual;
        return $this;
    }
    
    public function add_button($id = "") {
        $this->button = CMobile_Element_Component_SideNav_Button::factory();
        $this->button->set_sidenav_id($this->id);
        $this->before()->add($this->button);
        return $this->button;
    }

    public function add_item($id = "") {
        $element = CMobile_Element_Component_SideNav_Item::factory($id);
        $this->menu_list->add($element);
        return $element;
    }

    public function add_menu($id = "") {
        $element = CMobile_Element_Component_SideNav_Menu::factory($id);
        $this->menu_list->add($element);
        return $element;
    }
    
    public function build() {
        $this->add_class('side-nav');
        if($this->is_fixed) {
            // $this->add_class('fixed');
        }
    }

    public function js($indent = 0) {
        $js = '';
        if(!$this->manual) {
            $js = '$(".button-collapse").sideNav();';

        }
        $js.=parent::js();
        return $js;
    }

}
