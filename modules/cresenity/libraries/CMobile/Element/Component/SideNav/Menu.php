<?php

class CMobile_Element_Component_SideNav_Menu extends CMobile_Element_AbstractComponent {

    protected $container;

    public function __construct($id = "") {
        parent::__construct($id);
        $this->tag = "div";
        $this->container = $this->add_ul();
    }
    
  
    public static function factory($id = "") {
        return new CMobile_Element_Component_SideNav_Menu($id);
    }

    public function add_item($id='') {
        $item = CMobile_Element_Component_SideNav_Item::factory($id);
        $this->container->add($item);
        return $item;

    }

    public function add_header($id='') {
        $item = CMobile_Element_Component_SideNav_Li_Header::factory($id);
        $this->container->add($item);
        return $item;
    }

    public function build() {
        $this->add_class('sidenav-body');
        $this->container->add_class('menu-list');
        // print_r($this->menu);
        // die();
        // $this->generate_menu($this->menu, true);
        // $this->add_attr('style', 'background-image: url(' . $this->background . ')');
    }

    
}
