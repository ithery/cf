<?php

class CMobile_Element_Component_SideNav_Li_Header extends CMobile_Element_AbstractComponent {

    //put your code here

    protected $background;

    public function __construct($id = "") {
        parent::__construct($id);
        $this->tag = "div";
    }
    
    public function set_background($background) {
        $this->background = $background;
        return $this;
    }
    
    public static function factory($id = "") {
        return new CMobile_Element_Component_SideNav_Li_Header($id);
    }

    public function build() {
        // $this->add_class('sidenav-top');
        $this->add_attr('style', 'background-image: url(' . $this->background . ')');
    }

}
