<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 17, 2018, 1:55:05 AM
 * @see CElement_Component_DataTable
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_DataTable {
    /**
     * @param string $fieldname
     *
     * @deprecated since version 1.2
     *
     * @return CElement_Component_DataTable_Column
     */
    public function add_column($fieldname) {
        return $this->addColumn($fieldname);
    }

    /**
     * @deprecated since version 1.2, please use setDataFromQuery
     *
     * @param mixed $q
     *
     * @return CElement_Component_DataTable
     */
    public function set_data_from_query($q) {
        return $this->setDataFromQuery($q);
    }

    /**
     * @deprecated since version 1.2, please use setAjax
     *
     * @param mixed $bool
     *
     * @return CElement_Component_DataTable
     */
    public function set_ajax($bool) {
        return $this->setAjax($bool);
    }

    /**
     * @deprecated since version 1.2, please use rowActionCount
     *
     * @return int
     */
    public function action_count() {
        return $this->rowActionCount();
    }

    /**
     * @deprecated since version 1.2, please use haveRowAction
     *
     * @return bool
     */
    public function have_action() {
        return $this->haveRowAction();
    }

    /**
     * @deprecated since version 1.2, please use addRowAction
     *
     * @param mixed $id
     *
     * @return CElement_Component_Action
     */
    public function add_row_action($id = '') {
        return $this->addRowAction($id);
    }

    /**
     * @deprecated since version 1.2, please use setRowActionStyle
     *
     * @param mixed $style
     *
     * @return CElement_Component_DataTable
     */
    public function set_action_style($style) {
        return $this->setRowActionStyle($style);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $filename
     *
     * @return $this
     */
    public function set_export_filename($filename) {
        return $this->setExportFilename($filename);
    }

    public function set_export_sheetname($sheetname) {
        return $this->setExportSheetname($sheetname);
    }

    public function set_domain($domain) {
        return $this->setDomain($domain);
    }

    public function set_database($db) {
        return $this->setDatabase($db);
    }

    public function set_table_striped($table_striped) {
        return $this->setTableStriped($table_striped);
    }

    public function set_widget_title($bool) {
        return $this->setWidgetTitle($bool);
    }

    public static function action_download_excel($data) {
        return static::actionDownloadExcel($data);
    }

    public function add_footer_action($id = '') {
        return $this->addFooterAction($id);
    }

    private static function export_excelxml_static($filename, $sheet_name = null, $table) {
        return static::exportExcelxmlStatic($filename, $sheet_name = null, $table);
    }

    public function have_footer_action() {
        return $this->haveFooterAction();
    }

    public function is_exported() {
        return $this->isExported();
    }

    /**
     * @deprecated since version 1.2
     *
     * @param string $title
     * @param bool   $lang
     *
     * @return $this
     */
    public function set_title($title, $lang = true) {
        return $this->setTitle($title, $lang);
    }

    public function set_dom($dom) {
        return $this->setDom($dom);
    }

    public function set_custom_column_header($html) {
        return $this->setCustomColumnHeader($html);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated 1.2 use setFooter
     */
    public function set_footer($bool) {
        return $this->setFooter($bool);
    }

    public function set_responsive($bool) {
        return $this->setResponsive($bool);
    }

    public function set_show_header($bool) {
        return $this->setShowHeader($bool);
    }

    /**
     * @param bool $quick_search
     *
     * @return $this
     *
     * @deprecated since 1.2 use setQuickSearch
     */
    public function set_quick_search($quick_search) {
        return $this->setQuickSearch($quick_search);
    }

    public function set_tbody_id($id) {
        return $this->setTbodyId($id);
    }

    /**
     * @param string $label
     * @param string $value
     * @param string $align
     * @param int    $labelcolspan
     *
     * @return $this
     *
     * @deprecated 1.2 use addFooterField
     */
    public function add_footer_field($label, $value, $align = 'left', $labelcolspan = 0) {
        return $this->addFooterField($label, $value, $align, $labelcolspan);
    }

    public function set_header_no_line_break($bool) {
        return $this->setHeaderNoLineBreak($bool);
    }

    public function have_header_action() {
        return $this->haveHeaderAction();
    }

    public function set_header_action_style($style) {
        return $this->setHeaderActionStyle($style);
    }

    public function header_action_count() {
        return $this->headerActionCount();
    }

    public function set_option($key, $val) {
        return $this->setOption($key, $val);
    }

    public function get_option($key) {
        return $this->getOption($key);
    }

    public function set_ajax_method($value) {
        return $this->setAjaxMethod($value);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated 1.2
     */
    public function set_apply_data_table($bool) {
        return $this->setApplyDataTable($bool);
    }

    /**
     * @param int $length
     *
     * @return $this
     *
     * @deprecated 1.2
     */
    public function set_display_length($length) {
        return $this->setDisplayLength($length);
    }

    /**
     * @param callable|Closure $func
     * @param string           $require
     *
     * @return $this
     *
     * @deprecated 1.2
     */
    public function cell_callback_func($func, $require = '') {
        return $this->cellCallbackFunc($func, $require);
    }

    /**
     * @param callable|Closure $func
     * @param string           $require
     *
     * @return $this
     *
     * @deprecated 1.2
     */
    public function filter_action_callback_func($func, $require = '') {
        return $this->filterActionCallbackFunc($func, $require);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $fieldname
     *
     * @return $this
     */
    public function set_key($fieldname) {
        return $this->setKey($fieldname);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $id
     *
     * @return $this
     */
    public function add_header_action($id = '') {
        return $this->addHeaderAction($id);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $bool
     *
     * @return $this
     */
    public function set_checkbox($bool) {
        return $this->setCheckbox($bool);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $val
     *
     * @return $this
     */
    public function set_checkbox_value($val) {
        return $this->setCheckboxValue($val);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $bool
     *
     * @return $this
     */
    public function set_header_sortable($bool) {
        return $this->setHeaderSortabel($bool);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $bool
     *
     * @return $this
     */
    public function set_numbering($bool) {
        return $this->setNumbering($bool);
    }

    /**
     * @deprecated since version 1.2
     *
     * @return $this
     */
    public function enable_numbering() {
        return $this->enableNumbering();
    }

    /**
     * @deprecated since version 1.2
     *
     * @return $this
     */
    public function disable_numbering() {
        return $this->disableNumbering();
    }

    /**
     * @deprecated since version 1.2
     *
     * @return $this
     */
    public function enable_checkbox() {
        return $this->enableCheckbox();
    }

    /**
     * @deprecated since version 1.2
     *
     * @return $this
     */
    public function disable_checkbox() {
        return $this->disableCheckbox();
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $q
     *
     * @return $this
     */
    public function set_query($q) {
        return $this->setQuery($q);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param mixed $el
     *
     * @return $this
     */
    public function set_data_from_elastic($el) {
        return $this->setDataFromElastic($el);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param array $a
     *
     * @return $this
     */
    public function set_data_from_array($a) {
        return $this->setDataFromArray($a);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param string $size
     *
     * @return $this
     */
    public function set_pdf_font_size($size) {
        return $this->setPdfFontSize($size);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param string $orientation
     *
     * @return $this
     */
    public function set_pdf_orientation($orientation) {
        return $this->setPdfOrientation($orientation);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param string $filename
     *
     * @return $this
     */
    public function export_pdf($filename) {
        return $this->exportPdf($filename);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param string $filename
     *
     * @return $this
     */
    public function export_excelcsv($filename) {
        return $this->exportExcelcsv($filename);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param string $filename
     * @param string $sheet_name
     *
     * @return $this
     */
    public function export_excelxml($filename, $sheet_name = null) {
        return $this->exportExcelxml($filename, $sheet_name);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param string $line
     *
     * @return $this
     */
    public function add_report_header($line) {
        return $this->addReportHeader($line);
    }

    /**
     * @deprecated since version 1.2
     *
     * @param string $filename
     * @param string $sheet_name
     *
     * @return $this
     */
    public function export_excel($filename, $sheet_name) {
        return $this->exportExcel($filename, $sheet_name);
    }
}
