<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_Component_Form_Field
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_Form_Field {
    /**
     * @param string $text
     * @param bool   $lang
     *
     * @deprecated since 1.2 use setLabel
     *
     * @return $this
     */
    public function set_label($text, $lang = true) {
        return $this->setLabel($text, $lang);
    }

    /**
     * @param null|string $style_form_group
     *
     * @deprecated since 1.2 use setStyleFormGroup
     *
     * @return $this
     */
    public function set_style_form_group($style_form_group) {
        return $this->setStyleFormGroup($style_form_group);
    }

    /**
     * @param string $id
     *
     * @deprecated since 1.2 use setGroupId
     *
     * @return $this
     */
    public function set_group_id($id) {
        return $this->setGroupId($id);
    }

    /**
     * @param string $class
     *
     * @deprecated since 1.2 use addGroupClass
     *
     * @return $this
     */
    public function add_group_class($class) {
        return $this->addGroupClass($class);
    }

    /**
     * @param string $key
     * @param string $val
     *
     * @deprecated since 1.2 use groupCustomCss
     *
     * @return $this
     */
    public function group_custom_css($key, $val) {
        return $this->groupCustomCss($key, $val);
    }

    /**
     * @param int|string $size
     *
     * @deprecated since 1.2 use setLabelSize
     *
     * @return $this
     */
    public function set_label_size($size) {
        return $this->setLabelSize($size);
    }

    /**
     * @param string $info_text
     *
     * @deprecated since 1.2 use setInfoText
     *
     * @return $this
     */
    public function set_info_text($info_text) {
        return $this->setInfoText($info_text);
    }

    /**
     * @deprecated since 1.2 use showLabel
     *
     * @return $this
     */
    public function show_label() {
        return $this->showLabel();
    }

    /**
     * @deprecated since 1.2 use hideLabel
     *
     * @return $this
     */
    public function hide_label() {
        return $this->hideLabel();
    }

    /**
     * @deprecated since 1.2 use styleFormInline
     *
     * @return $this
     */
    public function style_form_inline() {
        return $this->styleFormInline();
    }

    /**
     * @param string $label_class
     *
     * @deprecated since 1.2 use addLabelClass
     *
     * @return $this
     */
    public function add_label_class($label_class) {
        return $this->addLabelClass($label_class);
    }

    /**
     * @param string $control_class
     *
     * @deprecated since 1.2 use addControlClass
     *
     * @return $this
     */
    public function add_control_class($control_class) {
        return $this->addControlClass($control_class);
    }

    /**
     * @deprecated since 1.2 use getInlineWithoutDefault
     *
     * @return string
     */
    public function get_inline_without_default() {
        return $this->getInlineWithoutDefault();
    }

    /**
     * @param string $inline_without_default
     *
     * @deprecated since 1.2 use setInlineWithoutDefault
     *
     * @return $this
     */
    public function set_inline_without_default($inline_without_default) {
        return $this->setInlineWithoutDefault($inline_without_default);
    }
}
