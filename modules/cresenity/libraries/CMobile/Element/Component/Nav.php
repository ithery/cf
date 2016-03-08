<?php

/**
 * Description of Nav
 *
 * @author Ecko Santoso
 * @since 04 Mar 16
 */
class CMobile_Element_Component_Nav  extends CMobile_Element_AbstractComponent {

    protected $wrapper = null;
    protected $container = null;
    protected $right_menu = null;
    protected $search = null;
    protected $input_div = null;
    protected $input_field = null;
    protected $search_label = null;
    
    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "nav";
        $this->wrapper = $this->add_div()->add_class('nav-wrapper');
        $this->container = $this->wrapper->add_div();
        $this->right_menu = $this->container->add_ul('navbar-right-menu')->add_class('right');
        
        $this->search = $this->wrapper->add_form('navbar-search')->custom_css('display', 'none');
        $this->input_div = $this->search->add_div()->add_class('input-field');
        $this->input_field = $this->input_div->add_control('navbar-search-input', 'search');
        $this->search_label = $this->input_div->add_label()->set_for_id('navbar-search-input');
        $this->search_label->add_icon()->set_icon('search')->set_type(''); 
        $this->input_div->add_icon('navbar-search-close')->set_icon('close')->set_type(''); 
    }
    
    public static function factory($id="") {
        return new CMobile_Element_Component_Nav($id);
    }
    
    public function add_right_menu($id="") {
        $element = CMobile_Element_Component_Nav_Item::factory($id);
        $this->right_menu->add($element);
        return $element;
    }
    public function add_brand($id="") {
        $element = CMobile_Element_Component_Nav_Brand::factory('logo-container');
        $this->wrapper->add($element);
        return $element;
    }
    
    public function build() {
        $this->add_class('nav-header');
    }
}