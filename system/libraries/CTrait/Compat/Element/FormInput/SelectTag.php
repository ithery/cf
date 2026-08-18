<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_FormInput_SelectTag
 *
 * None of these properties exist on CElement_FormInput_SelectTag (or its
 * parents), so every setter below just writes an undefined dynamic property
 * with no observable effect.
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_SelectTag {
    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_multiple($bool) {
        /** @var CElement_FormInput_SelectTag $this */
        $this->multiple = $bool;

        return $this;
    }

    /**
     * @param int $min_input_length
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_min_input_length($min_input_length) {
        /** @var CElement_FormInput_SelectTag $this */
        $this->min_input_length = $min_input_length;

        return $this;
    }

    /**
     * @param string $key_field
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_key_field($key_field) {
        /** @var CElement_FormInput_SelectTag $this */
        $this->key_field = $key_field;

        return $this;
    }

    /**
     * @param string $search_field
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_search_field($search_field) {
        /** @var CElement_FormInput_SelectTag $this */
        $this->search_field = $search_field;

        return $this;
    }

    /**
     * @param mixed $query
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_query($query) {
        /** @var CElement_FormInput_SelectTag $this */
        $this->query = $query;

        return $this;
    }

    /**
     * @param string $fmt
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_format_result($fmt) {
        /** @var CElement_FormInput_SelectTag $this */
        $this->format_result = $fmt;

        return $this;
    }

    /**
     * @param string $fmt
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_format_selection($fmt) {
        /** @var CElement_FormInput_SelectTag $this */
        $this->format_selection = $fmt;

        return $this;
    }

    /**
     * @param string $placeholder
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_placeholder($placeholder) {
        /** @var CElement_FormInput_SelectTag $this */
        $this->placeholder = $placeholder;

        return $this;
    }
}
