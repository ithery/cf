<?php

defined('SYSPATH') or die('No direct access allowed.');

//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_SelectSearch {
    protected $format_selection;

    protected $format_result;

    protected $key_field;

    protected $search_field;

    protected $min_input_length;

    protected $dropdown_classes;

    /**
     * @param bool $bool
     *
     * @deprecated 1.3
     *
     * @return $this
     */
    public function set_multiple($bool = true) {
        return $this->setMultiple($bool);
    }

    /**
     * @param int $val
     *
     * @deprecated 1.3
     *
     * @return $this
     */
    public function set_delay($val) {
        return $this->setDelay($val);
    }

    /**
     * @param bool $bool
     *
     * @deprecated 1.3
     *
     * @return $this
     */
    public function set_auto_select($bool = true) {
        return $this->setAutoSelect($bool);
    }

    /**
     * @param int $minInputLength
     *
     * @deprecated 1.3
     *
     * @return $this
     */
    public function set_min_input_length($minInputLength) {
        return $this->setMinInputLength($minInputLength);
    }

    /**
     * @param string $keyField
     *
     * @deprecated 1.3
     *
     * @return $this
     */
    public function set_key_field($keyField) {
        return $this->setKeyField($keyField);
    }

    /**
     * @param array|string $searchField
     *
     * @deprecated 1.3
     *
     * @return $this
     */
    public function set_search_field($searchField) {
        return $this->setSearchField($searchField);
    }

    /**
     * @param string $query
     *
     * @deprecated 1.3
     *
     * @return $this
     */
    public function set_query($query) {
        return $this->setQuery($query);
    }

    /**
     * @param string|Closure $fmt
     *
     * @deprecated 1.3
     *
     * @return $this
     */
    public function set_format_result($fmt) {
        return $this->setFormatResult($fmt);
    }

    /**
     * @param string|Closure $fmt
     *
     * @deprecated 1.3
     *
     * @return $this
     */
    public function set_format_selection($fmt) {
        return $this->setFormatSelection($fmt);
    }

    /**
     * @param string $placeholder
     *
     * @deprecated 1.3
     *
     * @return $this
     */
    public function set_placeholder($placeholder) {
        /** @var CElement_FormInput_SelectSearch $this */
        return $this->setPlaceholder($placeholder);
    }

    /**
     * @param string|array $c
     *
     * @deprecated 1.3
     *
     * @return $this
     */
    public function add_dropdown_class($c) {
        if (is_array($c)) {
            $this->dropdown_classes = array_merge($c, $this->dropdown_classes);
        } else {
            $this->dropdown_classes[] = $c;
        }

        return $this->addDropdownClass($c);
    }

    /**
     * @deprecated 1.3
     *
     * @return string
     */
    public function create_ajax_url() {
        return $this->createAjaxUrl();
    }
}
