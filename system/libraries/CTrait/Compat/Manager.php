<?php

//@codingStandardsIgnoreStart

/**
 * @see CManager
 */
trait CTrait_Compat_Manager {
    /**
     * @param type $type
     * @param type $class
     * @param type $code_path
     *
     * @return $this
     *
     * @deprecated plase use registerControl
     */
    public function register_control($type, $class, $code_path = '') {
        return $this->registerControl($type, $class, $code_path);
    }

    /**
     * @param type $type
     * @param type $class
     * @param type $code_path
     *
     * @return $this
     *
     * @deprecated plase use registerElement
     */
    public function register_element($type, $class, $code_path = '') {
        return $this->registerElement($type, $class, $code_path);
    }

    /**
     * @param string $type Type of control to check
     *
     * @return bool
     *
     * @deprecated plase use isRegisteredControl
     */
    public function is_registered_control($type) {
        return $this->isRegisteredControl($type);
    }

    /**
     * @return array
     *
     * @deprecated plase use getRegisteredControl
     */
    public function get_registered_controls() {
        return $this->getRegisteredControl();
    }

    /**
     * @param string $type Type of element to check
     *
     * @return bool
     *
     * @deprecated plase use isRegisteredElement
     */
    public function is_registered_element($type) {
        return $this->isRegisteredElement($type);
    }

    /**
     * @param string $id
     * @param string $type
     *
     * @return CElement_FormInput
     *
     * @throws CException
     *
     * @deprecated plase use createControl
     */
    public function create_control($id, $type) {
        return $this->createControl($id, $type);
    }

    /**
     * @param string $id
     * @param string $type
     *
     * @return CElement_Element
     *
     * @throws CException
     *
     * @deprecated plase use createElement
     */
    public function create_element($id, $type) {
        return $this->createElement($id, $type);
    }

    /**
     * @param type $path
     *
     * @return $this
     *
     * @deprecated plase use setMobilePath
     */
    public function set_mobile_path($path) {
        return $this->setMobilePath($path);
    }

    /**
     * @return this
     *
     * @deprecated plase use getMobilePath
     */
    public function get_mobile_path() {
        return $this->getMobilePath();
    }

    /**
     * @return this
     *
     * @deprecated plase use isMobile
     */
    public function is_mobile() {
        return $this->isMobile();
    }

    /**
     * @return array
     *
     * @deprecated plase use isMobile
     */
    public function get_theme_data() {
        return $this->getThemeData();
    }

    /**
     * @param array $theme_data
     *
     * @return $this
     *
     * @deprecated plase use setThemeData
     */
    public function set_theme_data($theme_data) {
        return $this->setThemeData($theme_data);
    }

    /**
     * Backward compatibility of registerModule
     *
     * @param string $module
     * @param array  $data   optional
     *
     * @return bool
     *
     * @deprecated 1.1, use registerModule
     */
    public static function register_module($module, $data = []) {
        return static::registerModule($module, $data);
    }

    /**
     * Undocumented function
     *
     * @param string $module
     *
     * @return void
     *
     * @deprecated 1.1, use unregisterModule
     */
    public static function unregister_module($module) {
        return static::unregisterModule($module);
    }
}
 //@codingStandardsIgnoreEnd
