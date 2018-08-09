<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 6:19:05 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_ActionList {

    protected $label_size;
    protected $btn_dropdown_classes;

    /**
     * @deprecated since version 1.2
     */
    public function set_style($style) {
        return $this->setStyle($style);
    }

    /**
     * @deprecated since version 1.2
     */
    public function set_label($label, $lang = true) {
        return $this->setLabel($label, $lang);
    }

    /**
     * @deprecated since version 1.2
     */
    public function set_label_size($label_size) {
        $this->label_size = $label_size;
        return $this;
    }

    /**
     * @deprecated since version 1.2
     */
    public function add_btn_dropdown_class($class) {
        $this->btn_dropdown_classes[] = $class;
        return $this;
    }

}
