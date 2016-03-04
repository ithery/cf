<?php

/**
 * Description of Nav
 *
 * @author Ecko Santoso
 * @since 04 Mar 16
 */
class CMobile_Element_Component_Nav  extends CMobile_Element_AbstractComponent {

    protected $wrapper = null;
    
    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "nav";
        $this->wrapper = $this->add_div()->add_class('nav-wrapper');
        
    }
    
    public static function factory($id="") {
        return new CMobile_Element_Component_Nav($id);
    }
    
    
    public function build() {
        $this->add_class('nav-header');
    }
}