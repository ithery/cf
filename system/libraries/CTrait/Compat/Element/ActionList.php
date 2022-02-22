<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 6:19:05 AM
 */
//@codingStandardsIgnoreStart

trait CTrait_Compat_Element_ActionList {
    protected $label_size;
    protected $btn_dropdown_classes;

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $style
     */
    public function set_style($style) {
        return $this->setStyle($style);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $label
     * @param mixed $lang
     */
    public function set_label($label, $lang = true) {
        return $this->setLabel($label, $lang);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $label_size
     */
    public function set_label_size($label_size) {
        $this->label_size = $label_size;
        return $this;
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $class
     */
    public function add_btn_dropdown_class($class) {
        $this->btn_dropdown_classes[] = $class;
        return $this;
    }
}
//@codingStandardsIgnoreEnd
