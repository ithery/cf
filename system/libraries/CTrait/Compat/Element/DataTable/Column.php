<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 12, 2018, 10:13:58 PM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_DataTable_Column {
    /**
     * @param mixed $text
     * @param mixed $lang
     *
     * @deprecated since version 1.2
     *
     * @return $this
     */
    public function set_label($text, $lang = true) {
        return $this->setLabel($text, $lang);
    }

    /**
     * @deprecated since version 1.2
     *
     * @return string
     */
    public function get_label() {
        return $this->getLabel();
    }

    /**
     * @return $string
     *
     * @deprecated since version 1.2
     */
    public function get_fieldname() {
        return $this->getFieldname();
    }

    /**
     * @return $string
     *
     * @deprecated since version 1.2
     */
    public function get_align() {
        return $this->getAlign();
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_hidden_phone($bool = true) {
        /** @var CElement_Component_DataTable_Column $this */
        return $this->setHiddenPhone($bool);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_hidden_tablet($bool = true) {
        /** @var CElement_Component_DataTable_Column $this */
        return $this->setHiddenTablet($bool);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_hidden_desktop($bool = true) {
        /** @var CElement_Component_DataTable_Column $this */
        return $this->setHiddenDesktop($bool);
    }

    public function set_input_type($type) {
        return $this->setInputType($type);
    }

    /**
     * @return $bool
     *
     * @deprecated since version 1.2
     */
    public function get_no_line_break() {
        return $this->getNoLineBreak();
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_no_line_break($bool) {
        return $this->setNoLineBreak($bool);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_visible($bool) {
        return $this->setVisible($bool);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_sortable($bool) {
        return $this->setSortable($bool);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_searchable($bool) {
        return $this->setSearchable($bool);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_editable($bool) {
        return $this->setEditable($bool);
    }

    public function set_width($w) {
        return $this->setWidth($w);
    }

    /**
     * @param string $align
     *
     * @return $this
     *
     * @deprecated since version 1.2
     */
    public function set_align($align) {
        return $this->setAlign($align);
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
        return $this->addTransform($name, $args);
    }

    public function set_format($s) {
        return $this->setFormat($s);
    }

    public function render_header_html($export_pdf, $th_class = '', $indent = 0) {
        return $this->renderHeaderHtml($export_pdf, $th_class, $indent);
    }
}
