<?php

class CMobile_Element_Component_SideNav_Item extends CMobile_Element_AbstractComponent {

    //put your code here
    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "li";
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_SideNav_Item($id);
    }

    public function add_menu($id='') {
        $menu = CMobile_Element_Component_SideNav_Menu::factory($id);
        $this->add($menu);
        return $menu;
    }

    public function add_header($id='') {
        $item = CMobile_Element_Component_SideNav_Li_Header::factory($id);
        $this->add($item);
        return $item;
    }
}
