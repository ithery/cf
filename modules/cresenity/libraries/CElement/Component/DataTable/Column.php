<?php

class CElement_Component_DataTable_Column extends CObject {

    public $transforms = array();
    public $fieldname;
    public $label;
    public $width;
    public $align;
    public $format;
    public $sortable;
    public $searchable;
    public $editable;
    public $visible;
    public $input_type;
    public $no_line_break;
    public $hidden_phone;
    public $hidden_tablet;
    public $hidden_desktop;

    public function __construct($fieldname) {
        parent::__construct();

        $this->fieldname = $fieldname;
        $this->align = "left";
        $this->label = "";
        $this->width = "";
        $this->transforms = array();
        $this->format = "";
        $this->sortable = true;
        $this->searchable = true;
        $this->visible = true;
        $this->input_type = "text";
        $this->editable = true;
        $this->no_line_break = false;
        $this->hidden_phone = false;
        $this->hidden_tablet = false;
        $this->hidden_desktop = false;
    }

    public static function factory($fieldname) {
        return new CElement_Component_DataTable_Column($fieldname);
    }

    public function get_fieldname() {
        return $this->fieldname;
    }

    public function get_align() {
        return $this->align;
    }

    public function set_hidden_phone($bool) {
        $this->hidden_phone = $bool;
        return $this;
    }

    public function set_hidden_tablet($bool) {
        $this->hidden_tablet = $bool;
        return $this;
    }

    public function set_hidden_desktop($bool) {
        $this->hidden_desktop = $bool;
        return $this;
    }

    public function set_input_type($type) {
        $this->input_type = $type;
        return $this;
    }

    public function get_no_line_break() {
        return $this->no_line_break;
    }

    public function set_no_line_break($bool) {
        $this->no_line_break = $bool;
        return $this;
    }

    public function set_visible($bool) {
        $this->visible = $bool;
        return $this;
    }

    public function set_sortable($bool) {
        $this->sortable = $bool;
        return $this;
    }

    public function set_searchable($bool) {
        $this->searchable = $bool;
        return $this;
    }

    public function set_editable($bool) {
        $this->editable = $bool;
        return $this;
    }

    public function set_label($text, $lang = true) {
        if ($lang)
            $text = clang::__($text);
        $this->label = $text;
        return $this;
    }

    public function set_width($w) {
        $this->width = $w;
        return $this;
    }

    public function set_align($al) {
        $this->align = $al;
        return $this;
    }

    public function get_label() {
        return $this->label;
    }

    public function add_transform($name, $args = array()) {
        $func = CDynFunction::factory($name);
        if (!is_array($args)) {
            $args = array($args);
        }
        foreach ($args as $arg) {
            $func->add_param($arg);
        }


        $this->transforms[] = $func;
        return $this;
    }

    public function set_format($s) {
        $this->format = $s;
        return $this;
    }

    public function render_header_html($export_pdf, $th_class = "", $indent = 0) {

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
        if ($this->hidden_phone)
            $class .= " hidden-phone";
        if ($this->hidden_tablet)
            $class .= " hidden-tablet";
        if ($this->hidden_desktop)
            $class .= " hidden-desktop";
        if ($export_pdf) {
            $html->appendln('<th ' . $pdf_thead_td_attr . ' field_name = "' . $this->fieldname . '" align="center" class="thead ' . $th_class . $class . '" scope="col"' . $addition_attr . '>' . $this->label . '</th>');
        } else {
            $html->appendln('<th ' . $pdf_thead_td_attr . ' field_name = "' . $this->fieldname . '" data-no-line-break="' . $data_no_line_break . '" data-align="' . $data_align . '" class="thead ' . $th_class . $class . '" scope="col"' . $addition_attr . '>' . $this->label . '</th>');
        }
        return $html->text();
    }

}

?>