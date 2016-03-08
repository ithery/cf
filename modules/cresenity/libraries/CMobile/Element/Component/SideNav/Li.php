<?php

/**
 * Description of Button
 *
 * @author Ecko Santoso
 * @since 04 Mar 16
 */
class CMobile_Element_Component_SideNav_Li extends CMobile_Element_AbstractComponent {

    //put your code here
    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "li";
    }

    public function add_header($id = "") {
        $element = CMobile_Element_Component_SideNav_Li_Header::factory($id);
        $this->add($element);
        return $element;
    }
    
    public static function factory($id = "") {
        return new CMobile_Element_Component_SideNav_Li($id);
    }

}
