<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
        return $this->setAjax($bool);
    }

    /**
     * @deprecated since version 1.2, please use rowActionCount
     *
     * @return int
     */
    public function action_count() {
        /** @var CElement_Component_DataTable $this */
        return $this->rowActionCount();
    }

    /**
     * @deprecated since version 1.2, please use haveRowAction
     *
     * @return bool
     */
    public function have_action() {
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
        return $this->setExportFilename($filename);
    }

    /**
     * @param string $sheetname
     *
     * @return $this
     *
     * @deprecated since version 1.2, please use setExportSheetname
     */
    public function set_export_sheetname($sheetname) {
        /** @var CElement_Component_DataTable $this */
        return $this->setExportSheetname($sheetname);
    }

    /**
     * @param mixed $domain
     *
     * @return $this
     *
     * @deprecated since version 1.2, please use setDomain
     */
    public function set_domain($domain) {
        /** @var CElement_Component_DataTable $this */
        return $this->setDomain($domain);
    }

    /**
     * @param mixed $db
     *
     * @return $this
     *
     * @deprecated since version 1.2, please use setDatabase
     */
    public function set_database($db) {
        /** @var CElement_Component_DataTable $this */
        return $this->setDatabase($db);
    }

    /**
     * @param bool $table_striped
     *
     * @return $this
     *
     * @deprecated since version 1.2, please use setTableStriped
     */
    public function set_table_striped($table_striped) {
        /** @var CElement_Component_DataTable $this */
        return $this->setTableStriped($table_striped);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2, please use setWidgetTitle
     */
    public function set_widget_title($bool) {
        /** @var CElement_Component_DataTable $this */
        return $this->setWidgetTitle($bool);
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     *
     * @deprecated since version 1.2, please use actionDownloadExcel
     */
    public static function action_download_excel($data) {
        return static::actionDownloadExcel($data);
    }

    /**
     * @param string $id
     *
     * @return CElement_Component_Action
     *
     * @deprecated since version 1.2, please use addFooterAction
     */
    public function add_footer_action($id = '') {
        /** @var CElement_Component_DataTable $this */
        return $this->addFooterAction($id);
    }

    /**
     * @param string $filename
     * @param null|string $sheet_name
     * @param mixed  $table
     *
     * @return mixed
     *
     * @deprecated since version 1.2, please use exportExcelxmlStatic
     */
    private static function export_excelxml_static($filename, $sheet_name = null, $table = null) {
        return static::exportExcelxmlStatic($filename, $sheet_name, $table);
    }

    /**
     * @return bool
     *
     * @deprecated since version 1.2, please use haveFooterAction
     */
    public function have_footer_action() {
        /** @var CElement_Component_DataTable $this */
        return $this->haveFooterAction();
    }

    /**
     * @return bool
     *
     * @deprecated since version 1.2, please use isExported
     */
    public function is_exported() {
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
        return $this->setTitle($title, $lang);
    }

    /**
     * @param string $dom
     *
     * @return $this
     *
     * @deprecated since version 1.2, please use setDom
     */
    public function set_dom($dom) {
        /** @var CElement_Component_DataTable $this */
        return $this->setDom($dom);
    }

    /**
     * @param string $html
     *
     * @return $this
     *
     * @deprecated since version 1.2, please use setCustomColumnHeader
     */
    public function set_custom_column_header($html) {
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
        return $this->setFooter($bool);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2, please use setResponsive
     */
    public function set_responsive($bool) {
        /** @var CElement_Component_DataTable $this */
        return $this->setResponsive($bool);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2, please use setShowHeader
     */
    public function set_show_header($bool) {
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
        return $this->setQuickSearch($quick_search);
    }

    /**
     * @param string $id
     *
     * @return $this
     *
     * @deprecated since version 1.2, please use setTbodyId
     */
    public function set_tbody_id($id) {
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
        return $this->addFooterField($label, $value, $align, $labelcolspan);
    }

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated since version 1.2, please use setHeaderNoLineBreak
     */
    public function set_header_no_line_break($bool) {
        /** @var CElement_Component_DataTable $this */
        return $this->setHeaderNoLineBreak($bool);
    }

    /**
     * @return bool
     *
     * @deprecated since version 1.2, please use haveHeaderAction
     */
    public function have_header_action() {
        /** @var CElement_Component_DataTable $this */
        return $this->haveHeaderAction();
    }

    /**
     * @param string $style
     *
     * @return $this
     *
     * @deprecated since version 1.2, please use setHeaderActionStyle
     */
    public function set_header_action_style($style) {
        /** @var CElement_Component_DataTable $this */
        return $this->setHeaderActionStyle($style);
    }

    /**
     * @return int
     *
     * @deprecated since version 1.2, please use headerActionCount
     */
    public function header_action_count() {
        /** @var CElement_Component_DataTable $this */
        return $this->headerActionCount();
    }

    /**
     * @param string $key
     * @param mixed  $val
     *
     * @return $this
     *
     * @deprecated since version 1.2, please use setOption
     */
    public function set_option($key, $val) {
        /** @var CElement_Component_DataTable $this */
        return $this->setOption($key, $val);
    }

    /**
     * @param string $key
     *
     * @return mixed
     *
     * @deprecated since version 1.2, please use getOption
     */
    public function get_option($key) {
        /** @var CElement_Component_DataTable $this */
        return $this->getOption($key);
    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @deprecated since version 1.2, please use setAjaxMethod
     */
    public function set_ajax_method($value) {
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
        return $this->setNumbering($bool);
    }

    /**
     * @deprecated since version 1.2
     *
     * @return $this
     */
    public function enable_numbering() {
        /** @var CElement_Component_DataTable $this */
        return $this->enableNumbering();
    }

    /**
     * @deprecated since version 1.2
     *
     * @return $this
     */
    public function disable_numbering() {
        /** @var CElement_Component_DataTable $this */
        return $this->disableNumbering();
    }

    /**
     * @deprecated since version 1.2
     *
     * @return $this
     */
    public function enable_checkbox() {
        /** @var CElement_Component_DataTable $this */
        return $this->enableCheckbox();
    }

    /**
     * @deprecated since version 1.2
     *
     * @return $this
     */
    public function disable_checkbox() {
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
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
        /** @var CElement_Component_DataTable $this */
        return $this->exportExcel($filename, $sheet_name);
    }
}
