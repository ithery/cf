<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CTrait_Compat_Manager {

    /**
     * 
     * @deprecated plase use registerControl
     * @param type $type
     * @param type $class
     * @param type $code_path
     * @return $this
     */
    public function register_control($type, $class, $code_path = '') {
        return $this->registerControl($type, $class, $code_path);
    }

    /**
     * 
     * @deprecated plase use registerElement
     * @param type $type
     * @param type $class
     * @param type $code_path
     * @return $this
     */
    public function register_element($type, $class, $code_path = '') {
        return $this->registerElement($type, $class, $code_path);
    }

    /**
     * 
     * @deprecated plase use isRegisteredControl
     * @param string $type Type of control to check
     * @return boolean
     */
    public function is_registered_control($type) {
        return $this->isRegisteredControl($type);
    }

    /**
     * 
     * @deprecated plase use getRegisteredControl
     * @return array
     */
    public function get_registered_controls() {
        return $this->getRegisteredControl();
    }

    /**
     * 
     * @deprecated plase use isRegisteredElement
     * @param string $type Type of element to check
     * @return boolean
     */
    public function is_registered_element($type) {
        return $this->isRegisteredElement($type);
    }

    /**
     * 
     * @deprecated plase use createControl
     * @param string $id
     * @param string $type
     * @return CElement_FormInput
     * @throws CException
     */
    public function create_control($id, $type) {
        return $this->createControl($id, $type);
    }

    /**
     * 
     * @deprecated plase use createElement
     * @param string $id
     * @param string $type
     * @return CElement_Element
     * @throws CException
     */
    public function create_element($id, $type) {
        return $this->createElement($id, $type);
    }

    /**
     * 
     * @deprecated plase use setMobilePath
     * @param type $path
     * @return $this
     */
    public function set_mobile_path($path) {
        return $this->setMobilePath($path);
    }

    /**
     * 
     * @deprecated plase use getMobilePath
     * @return this
     */
    public function get_mobile_path() {
        return $this->getMobilePath();
    }

    /**
     * 
     * @deprecated plase use isMobile
     * @return this
     */
    public function is_mobile() {
        return $this->isMobile();
    }

    /**
     * 
     * @deprecated plase use isMobile
     * @return array
     */
    public function get_theme_data() {
        return $this->getThemeData();
    }

    /**
     * 
     * @deprecated plase use isMobile
     * @param array $theme_data
     * @return $this
     */
    public function set_theme_data($theme_data) {
        return $this->setThemeData($theme_data);
    }

}
