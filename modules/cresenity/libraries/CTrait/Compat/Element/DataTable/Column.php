<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 12, 2018, 10:13:58 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_DataTable_Column {

    /**
     * 
     * @deprecated since version 1.2
     * @return $this
     */
    public function set_label($text, $lang = true) {
        return $this->setLabel($text, $lang);
    }

    /**
     * 
     * @deprecated since version 1.2
     * @return string
     */
    public function get_label() {
        return $this->getLabel();
    }
    
    public function get_fieldname() {
        return $this->getFieldname();
    }
    
    public function get_align() {
        return $this->getAlign();
    }
    
    public function set_hidden_phone($bool) {
        return $this->setHiddenPhone($bool);
    }
    
    public function set_hidden_tablet($bool) {
        return $this->setHiddenTablet($bool);
    }
    
    public function set_hidden_desktop($bool) {
        return $this->setHiddenDesktop($bool);
    }
    
    public function set_input_type($type) {
        return $this->setInputType($type);
    }
    
    public function get_no_line_break() {
        return $this->getNoLineBreak();
    }
    
    public function set_no_line_break($bool) {
        return $this->setNoLineBreak($bool);
    }
    
    public function set_visible($bool) {
        return $this->setVisible($bool);
    }
    
    public function set_sortable($bool) {
        return $this->setSortable($bool);
    }
    
    public function set_searchable($bool) {
        return $this->setSearchable($bool);
    }
    
    public function set_editable($bool) {
        return $this->setEditable($bool);
    }
    
    public function set_width($w) {
        return $this->setWidth($w);
    }
    
    public function set_align($al) {
        return $this->setAlign($al);
    }
    
    public function add_transform($name, $args = array()) {
        return $this->addTransform($name, $args);
    }
    
    public function set_format($s) {
        return $this->setFormat($s);
    }
    
    public function render_header_html($export_pdf, $th_class = "", $indent = 0) {
        return $this->renderHeaderHtml($export_pdf, $th_class, $indent);
    }

}
