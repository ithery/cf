<?php

class CMobile_Element_Component_SideNav_Button extends CMobile_Element_AbstractComponent {

    //put your code here

    protected $sidenav_id;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
    }
    
    public function set_sidenav_id($id) {
        $this->sidenav_id = $id;
        return $this;
    }
    

    public static function factory($id = "") {
        return new CMobile_Element_Component_SideNav_Button($id);
    }

    public function build() {
        $this->add_class('button-collapse show-on-large');
        $this->id = 'nav-mobile-button';
        $this->set_attr('data-activates',$this->sidenav_id);
        $this->add_icon()->add('menu');
    }

}
