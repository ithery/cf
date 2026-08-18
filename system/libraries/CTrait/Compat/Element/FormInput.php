<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_FormInput
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput {
    /**
     * @param mixed $val
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_value($val) {
        /** @var CElement_FormInput $this */
        return $this->setValue($val);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_submit_onchange($bool) {
        /** @var CElement_FormInput $this */
        return $this->setSubmitOnChange($bool);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_ajax($bool) {
        /** @var CElement_FormInput $this */
        return $this->setAjax($bool);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_disabled($bool) {
        /** @var CElement_FormInput $this */
        return $this->setDisabled($bool);
    }

    /**
     * @param int $size
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_size($size) {
        /** @var CElement_FormInput $this */
        return $this->setSize($size);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_readonly($bool) {
        /** @var CElement_FormInput $this */
        return $this->setReadOnly($bool);
    }

    /**
     * @return string
     *
     * @deprecated since version 1.2
     */
    public function get_field_id() {
        /** @var CElement_FormInput $this */
        return $this->getFieldId();
    }

    /**
     * @param string $name
     * @param array  $args
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function add_transform($name, $args = []) {
        /** @var CElement_FormInput $this */
        return $this->addTransform($name, $args);
    }

    /**
     * @param array $list
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_list($list) {
        /** @var CElement_FormInput $this */
        return $this->setList($list);
    }

    /**
     * @param string $val
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_name($val) {
        /** @var CElement_FormInput $this */
        return $this->setName($val);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function add_validation($name, $value = '') {
        /** @var CElement_FormInput $this */
        return $this->addValidation($name, $value);
    }

    /**
     * @param string $type
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_type($type) {
        /** @var CElement_FormInput $this */
        return $this->setType($type);
    }

    /**
     * @param string $text
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_on_text($text) {
        /** @var CElement_FormInput $this */
        return $this->setOnText($text);
    }

    /**
     * @param string $text
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_off_text($text) {
        /** @var CElement_FormInput $this */
        return $this->setOffText($text);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_checked($bool) {
        /** @var CElement_FormInput $this */
        return $this->setChecked($bool);
    }

    /**
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function show_updown() {
        /** @var CElement_FormInput $this */
        return $this->showUpdown();
    }

    /**
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function hide_updown() {
        /** @var CElement_FormInput $this */
        return $this->hideUpdown();
    }

    /**
     * @return string
     *
     * @deprecated since version 1.2
     */
    protected function html_attr() {
        /** @var CElement_FormInput $this */
        return $this->htmlAttr();
    }
}
