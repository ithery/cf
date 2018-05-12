<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 4:48:48 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_DataTable_Column {

    use CTrait_Element_Property_Label,
        CTrait_Element_Property_Align,
        CTrait_Element_Property_Visible,
        CTrait_Element_Behavior_Sortable,
        CTrait_Element_Behavior_Searchable,
        CTrait_Element_Behavior_Editable,
        CTrait_Element_Transform,
        CTrait_Element_Responsive,
        CTrait_Compat_Element_Table_Column;

    protected $fieldName;
    protected $format;
    protected $inputType;
    protected $noLineBreak;

    protected $dataTable;

    public function __construct($fieldName, CElement_Component_DataTable $dataTable) {
        $this->dataTable = $dataTable;

        $this->fieldName = $fieldName;
        $this->align = "left";
        $this->label = "";
        $this->width = "";
        $this->transforms = array();
        $this->format = "";
        $this->sortable = true;
        $this->searchable = true;
        $this->visible = true;
        $this->inputType = "text";
        $this->editable = true;
        $this->noLineBreak = false;
        $this->hiddenPhone = false;
        $this->hiddenTablet = false;
        $this->hiddenDesktop = false;
    }

    public function getFieldName() {
        return $this->fieldName;
    }

    

    public function setInputType($type) {
        $this->inputType = $type;
        return $this;
    }

    public function getNoLineBreak() {
        return $this->noLineBreak;
    }

    public function setNoLineBreak($bool) {
        $this->no_line_break = $bool;
        return $this;
    }

    /**
     * Set format of column
     * 
     * @param string $s
     * @return $this
     */
    public function setFormat($s) {
        $this->format = $s;
        return $this;
    }
    
    /**
     * Get the format of this column
     * 
     * @return string
     */
    public function getFormat() {
        return $this->format;
    }

    public function renderHeaderHtml($export_pdf, $th_class = "", $indent = 0) {

        $pdf_thead_td_attr = '';
        if ($export_pdf) {


            $pdf_thead_td_attr = ' bgcolor="#9f9f9f" color="#000"  ';
        }
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $addition_attr = "";
        if (strlen($this->width) > 0) {
            $addition_attr .= ' width="' . $this->width . '"';
        }
        $class = "";
        $data_align = "";
        switch ($this->get_align()) {
            case "left": $data_align .= "align-left";
                break;
            case "right": $data_align .= "align-right";
                break;
            case "center": $data_align .= "align-center";
                break;
        }
        $data_no_line_break = "";
        if ($this->get_no_line_break()) {
            $data_no_line_break = "no-line-break";
        }
        if ($export_pdf) {
            switch ($this->get_align()) {
                case "left": $pdf_thead_td_attr .= ' align="left"';
                    break;
                case "right": $pdf_thead_td_attr .= ' align="right"';
                    break;
                case "center": $pdf_thead_td_attr .= ' align="center"';
                    break;
            }
        }
        if ($this->sortable)
            $class .= " sortable";
        if ($this->hiddenPhone)
            $class .= " hidden-phone";
        if ($this->hiddenTablet)
            $class .= " hidden-tablet";
        if ($this->hiddenDesktop)
            $class .= " hidden-desktop";
        if ($export_pdf) {
            $html->appendln('<th ' . $pdf_thead_td_attr . ' field_name = "' . $this->fieldName . '" align="center" class="thead ' . $th_class . $class . '" scope="col"' . $addition_attr . '>' . $this->label . '</th>');
        } else {
            $html->appendln('<th ' . $pdf_thead_td_attr . ' field_name = "' . $this->fieldName . '" data-no-line-break="' . $data_no_line_break . '" data-align="' . $data_align . '" class="thead ' . $th_class . $class . '" scope="col"' . $addition_attr . '>' . $this->label . '</th>');
        }
        return $html->text();
    }

}
