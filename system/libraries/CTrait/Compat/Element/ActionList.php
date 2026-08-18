<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_List_ActionList
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_ActionList {
    /**
     * @param string $style
     *
     * @return CElement_List_ActionList
     *
     * @deprecated since version 1.2, use setStyle
     */
    public function set_style($style) {
        /** @var CElement_List_ActionList $this */
        return $this->setStyle($style);
    }

    /**
     * @param string     $label
     * @param bool|array $lang
     *
     * @return CElement_List_ActionList
     *
     * @deprecated since version 1.2, use setLabel
     */
    public function set_label($label, $lang = true) {
        /** @var CElement_List_ActionList $this */
        return $this->setLabel($label, $lang);
    }

    /**
     * @param int $label_size
     *
     * @return CElement_List_ActionList
     *
     * @deprecated since version 1.2
     */
    public function set_label_size($label_size) {
        /** @var CElement_List_ActionList $this */
        $this->label_size = $label_size;

        return $this;
    }

    /**
     * @param string $class
     *
     * @return CElement_List_ActionList
     *
     * @deprecated since version 1.2
     */
    public function add_btn_dropdown_class($class) {
        /** @var CElement_List_ActionList $this */
        $this->btn_dropdown_classes[] = $class;

        return $this;
    }
}
//@codingStandardsIgnoreEnd
