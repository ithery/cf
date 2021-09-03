<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 2:29:43 AM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_Form_Field {
    public function set_label($text, $lang = true) {
        return $this->setLabel($text, $lang);
    }

    public function toarray() {
        return $this->toArray();
    }

    public function set_style_form_group($style_form_group) {
        return $this->setStyleFormGroup($style_form_group);
    }

    public function set_group_id($id) {
        return $this->setGroupId($id);
    }

    public function add_group_class($class) {
        return $this->addGroupClass($class);
    }

    public function group_custom_css($key, $val) {
        return $this->groupCustomCss($key, $val);
    }

    public function set_label_size($size) {
        return $this->setLabelSize($size);
    }

    public function set_info_text($info_text) {
        return $this->setInfoText($info_text);
    }

    public function show_label() {
        return $this->showLabel();
    }

    public function hide_label() {
        return $this->hideLabel();
    }

    public function style_form_inline() {
        return $this->styleFormInline();
    }

    public function add_label_class($label_class) {
        return $this->addLabelClass($label_class);
    }

    public function add_control_class($control_class) {
        return $this->addControlClass($control_class);
    }

    public function get_inline_without_default() {
        return $this->getInlineWithoutDefault();
    }

    public function set_inline_without_default($inline_without_default) {
        return $this->setInlineWithoutDefault($inline_without_default);
    }
}
