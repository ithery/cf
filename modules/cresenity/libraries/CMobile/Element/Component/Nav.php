<?php

/**
 * Description of Nav
 *
 * @author Hery
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
    protected $url;
    protected $method;
    protected $search_value;
    
    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "nav";
        $this->wrapper = $this->add_div()->add_class('nav-wrapper');
        $this->container = $this->wrapper->add_div();
        $this->right_menu = $this->container->add_ul('navbar-right-menu')->add_class('right ul-top-menu');
        $this->method = 'POST';
        $this->search_value = '';
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
    
    public function set_url($param) {
        $this->url = $param;
        return $this;
    }
    
    public function set_method($method) {
        $this->method = $method;
        return $this;
    }
    
    public function set_search_value($search_value) {
        $this->search_value = $search_value;
        return $this;
    }
    
    public function build() {
        $this->add_class('nav-header');
        $form = $this->wrapper->add_div('navbar-search')->custom_css('display', 'none')->add_class('searchform');
        if (strlen($this->url) > 0) {
            // $form->set_action($this->url);
        }
        if (strlen($this->method) > 0) {
            // $form->set_method($this->method);
        }
        $this->search = $form;
        $this->input_div = $this->search->add_div()->add_class('input-field');
        $this->input_field = $this->input_div->add_control('keyword', 'search')->add_class('keyword-search');
        if (strlen($this->search_value) > 0) {
            $this->input_field->set_value($this->search_value);
        }
        $this->search_label = $this->input_div->add_label()->set_for_id('navbar-search-input');
        $search_icon = $this->search_label->add_icon()->set_icon('search')->set_type(''); 
        $search_icon->custom_css('position', 'absolute');
        $search_icon->custom_css('left', '1rem');
        $this->input_div->add_icon('navbar-search-close')->set_icon('close')->set_type('')->add_class('searchbar-close'); 
    }
}