<?php

class CMobile_Element_Component_SideNav_Button extends CMobile_Element_AbstractComponent {

    //put your code here

    protected $sidenav_id;
    protected $type;
    protected $href;
    protected $icon;

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
        $this->type = "menu";
        $this->href = "";
        $this->icon = "menu";
    }
    
    public function set_sidenav_id($id) {
        $this->sidenav_id = $id;
        return $this;
    }

    public function set_type($type) {
        $this->type = $type;
        return $this;
    }

    public function set_href($href) {
        $this->href = $href;
        return $this;
    }

    public function set_icon($icon) {
        $this->icon = $icon;
        return $this;
    }
    
    public static function factory($id = "") {
        return new CMobile_Element_Component_SideNav_Button($id);
    }

    public function build() {
        // echo $this->type;
        // die();
        if($this->type == 'menu') {
            $this->add_class('button-collapse show-on-large');
            $this->id = 'nav-mobile-button';
            $this->set_attr('data-activates',$this->sidenav_id);
            $this->add_icon()->add('menu');
        } else {
            $this->tag = "a";
            // $this->add_class('button-collapse show-on-large');
            $this->id = 'nav-mobile-button';
            // $this->set_attr('data-activates',$this->sidenav_id);
            $this->set_attr('href',$this->href);
            $this->custom_css('position', 'relative');
            $this->custom_css('float', 'left');
            $this->add_icon()->add($this->icon);
        }
    }

}
