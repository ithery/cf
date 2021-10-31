<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 2:29:43 AM
 * @see CElement_FormInput
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput {
    /**
     * @param mixed $val
     *
     * @deprecated since version 1.2
     *
     * @return $this
     */
    public function set_value($val) {
        return $this->setValue($val);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $bool
     *
     * @return $this
     */
    public function set_submit_onchange($bool) {
        return $this->setSubmitOnChange($bool);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $bool
     */
    public function set_ajax($bool) {
        return $this->setAjax($bool);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $size
     * @param mixed $bool
     */
    public function set_disabled($bool) {
        return $this->setDisabled($bool);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $size
     */
    public function set_size($size) {
        return $this->setSize($size);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $bool
     */
    public function set_readonly($bool) {
        return $this->setReadOnly($bool);
    }

    /**
     * @deprecated since version 1.2
     */
    public function get_field_id() {
        return $this->getFieldId();
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $name
     * @param mixed $args
     */
    public function add_transform($name, $args = []) {
        return $this->addTransform($name, $args);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param array $list
     *
     * @return $this
     */
    public function set_list($list) {
        return $this->setList($list);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $val
     */
    public function set_name($val) {
        return $this->setName($val);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $name
     * @param mixed $value
     */
    public function add_validation($name, $value = '') {
        return $this->addValidation($name, $value);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $type
     */
    public function set_type($type) {
        return $this->setType($type);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $text
     */
    public function set_on_text($text) {
        return $this->setOnText($text);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $text
     */
    public function set_off_text($text) {
        return $this->setOffText($text);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $bool
     */
    public function set_checked($bool) {
        return $this->setChecked($bool);
    }

    /**
     * @deprecated since version 1.2
     */
    public function show_updown() {
        return $this->showUpdown();
    }

    /**
     * @deprecated since version 1.2
     */
    public function hide_updown() {
        return $this->hideUpdown();
    }

    /**
     * @deprecated since version 1.2
     */
    protected function html_attr() {
        return $this->htmlAttr();
    }
}
