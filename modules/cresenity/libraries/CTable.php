<?php

    class CTable extends CElement {

        public $default_paging_list = array(
            "10" => "10",
            "25" => "25",
            "50" => "50",
            "100" => "100",
            "-1" => "ALL",
        );
        public $db;
        public $db_config;
        public $columns;
        public $footer;
        public $footer_field;
        public $row_action_list;
        public $header_action_list;
        public $header_action_style;
        public $requires = array();
        public $data;
        public $key_field;
        public $checkbox;
        public $checkbox_value;
        public $numbering;
        public $query;
        public $custom_column_header;
        public $header_sortable;
        public $cell_callback_func;
        public $display_length;
        public $paging_list;
        public $storage;
        public $responsive;
        public $options;
        public $apply_data_table;
        public $group_by;
        public $title;
        public $ajax;
        public $ajax_method;
        public $icon;
        public $editable_form;
        public $can_edit;
        public $can_add;
        public $can_delete;
        public $can_view;
        public $action_style;
        public $header_no_line_break;
        public $export_xml;
        public $export_excel;
        public $export_excelxml;
        public $export_excelcsv;
        public $export_pdf;
        public $pdf_font_size;
        public $pdf_orientation;
        public $show_header;
        public $report_header = array();
        public $footer_action_list = array();
        public $footer_action_style;
        protected $quick_search = FALSE;
        protected $tbody_id;
        protected $js_cell;

        public function __construct($id = "") {
            parent::__construct($id);
            $this->default_paging_list["-1"] = clang::__("ALL");
            $this->tag = "table";
            $this->responsive = false;
            $this->db = CDatabase::instance();
            $this->db_config = $this->db->config();
            $this->display_length = "10";
            $this->paging_list = $this->default_paging_list;
            $this->storage = new CList;
            $this->options = CTableOptions::factory();
            $this->data = array();
            $this->key_field = "";
            $this->columns = array();
            $this->row_action_list = CActionList::factory();
            $this->header_action_list = CActionList::factory();
            $this->header_action_style = 'widget-action';
            $this->header_action_list->set_style('widget-action');
            $this->action_style = 'btn-icon-group';
            $this->row_action_list->set_style('btn-icon-group');
            $this->checkbox = false;
            $this->checkbox_value = array();
            $this->numbering = false;
            $this->query = '';
            $this->header_sortable = true;
            $this->footer = false;
            $this->footer_field = array();
            $this->cell_callback_func = "";
            $this->display_length = "10";
            $this->ajax = false;
            $this->ajax_method = "get";
            $this->title = "";
            $this->editable_form = null;
            $this->can_edit = false;
            $this->can_add = false;
            $this->can_delete = false;
            $this->can_view = false;
            $this->export_pdf = false;
            $this->export_excelxml = false;
            $this->export_excelcsv = false;
            $this->export_xml = false;
            $this->export_excel = false;
            $this->header_no_line_break = false;

            $this->custom_column_header = "";
            $this->show_header = true;
            $this->storage = null;
            $this->apply_data_table = true;
            $this->group_by = "";
            $this->icon = "";
            $this->pdf_font_size = 8;
            $this->pdf_orientation = 'P';
            $this->requires = array();
            $this->js_cell = '';
            $this->quick_search = FALSE;
            $this->tbody_id = '';

            $this->report_header = array();
            $this->footer_action_list = CActionList::factory();
            $this->footer_action_style = 'btn-dropdown';
            $this->footer_action_list->set_style('btn-dropdown');

            //$this->add_footer_action('export_excel');

            CClientModules::instance()->register_module('jquery.datatable');
        }

        public static function factory($id = "") {
            return new CTable($id);
        }

        public function set_database($db) {
            $this->db = $db;
            $this->db_config = $db->config();

            return $this;
        }

        public function add_footer_action($id = "") {
            $row_act = CAction::factory($id);
            $this->footer_action_list->add($row_act);
            if ($id == 'export_excel') {
                $row_act->set_label('Download Excel');
                $row_act->add_listener('click')->add_handler('custom')->set_js("alert('a');");
            }
            return $row_act;
        }

        public function have_footer_action() {
            //return $this->can_edit||$this->can_delete||$this->can_view;
            return $this->footer_action_list->child_count() > 0;
        }

        public function is_exported() {
            return $this->export_excel || $this->export_excelxml || $this->export_excelcsv || $this->export_pdf;
        }

        public function set_title($title, $lang = true) {
            if ($lang) $title = clang::__($title);
            $this->title = $title;
            return $this;
        }

        public function set_custom_column_header($html) {
            $this->custom_column_header = $html;
            return $this;
        }

        public function set_footer($bool) {
            $this->footer = $bool;
            return $this;
        }

        public function set_responsive($bool) {
            $this->responsive = $bool;
            return $this;
        }

        public function set_show_header($bool) {
            $this->show_header = $bool;
            return $this;
        }

        public function set_quick_search($quick_search) {
            $this->quick_search = $quick_search;
            return $this;
        }

        public function set_tbody_id($id) {
            $this->tbody_id = $id;
            return $this;
        }

        public function add_footer_field($label, $value, $align = "left", $labelcolspan = 0) {
            $f = array(
                "label" => $label,
                "value" => $value,
                "align" => $align,
                "labelcolspan" => $labelcolspan,
            );
            $this->footer_field[] = $f;
            return $this;
        }

        public function set_header_no_line_break($bool) {
            $this->header_no_line_break = $bool;
            return $this;
        }

        public function have_action() {
            //return $this->can_edit||$this->can_delete||$this->can_view;
            return $this->row_action_list->child_count() > 0;
        }

        public function have_header_action() {
            //return $this->can_edit||$this->can_delete||$this->can_view;
            return $this->header_action_list->child_count() > 0;
        }

        public function set_action_style($style) {
            $this->action_style = $style;
            $this->row_action_list->set_style($style);
        }

        public function set_header_action_style($style) {
            $this->header_action_style = $style;
            $this->header_action_list->set_style($style);
        }

        public function action_count() {
            return $this->row_action_list->child_count();
        }

        public function header_action_count() {
            return $this->header_action_list->child_count();
        }

        public function set_option($key, $val) {
            $this->options->add($key, $val);
            return $this;
        }

        public function get_option($key) {
            return $this->options->get_by_name($key);
        }

        public function set_ajax($bool) {
            $this->ajax = $bool;
            return $this;
        }

        public function set_ajax_method($value) {
            $this->ajax_method = $value;
            return $this;
        }

        public function set_apply_data_table($bool) {
            $this->apply_data_table = $bool;
            return $this;
        }

        public function set_display_length($length) {
            $this->display_length = $length;
            return $this;
        }

        public function cell_callback_func($func, $require = "") {
            $this->cell_callback_func = $func;
            if (strlen($require) > 0) {
                $this->requires[] = $require;
            }
            return $this;
        }

        public function set_key($fieldname) {
            $this->key_field = $fieldname;
            return $this;
        }

        public function add_column($fieldname) {
            $col = CTableColumn::factory($fieldname);
            $this->columns[] = $col;
            return $col;
        }

        public function set_group_by($group_by) {
            $this->group_by = $group_by;
            return $this;
        }

        public function add_row_action($id = "") {
            $row_act = CAction::factory($id);
            $this->row_action_list->add($row_act);
            return $row_act;
        }

        public function add_header_action($id = "") {
            return $this->header_action_list->add_action($id);
        }

        public function set_checkbox($bool) {
            $this->checkbox = $bool;
            return $this;
        }

        public function set_checkbox_value($val) {
            if (!is_array($val)) $val = array($val);
            $this->checkbox_value = $val;
            return $this;
        }

        public function set_header_sortable($bool) {
            $this->header_sortable = $bool;
            return $this;
        }

        public function set_numbering($bool) {
            $this->numbering = $bool;
            return $this;
        }

        public function enable_numbering() {
            $this->numbering = true;
            return $this;
        }

        public function disable_numbering() {
            $this->numbering = false;
            return $this;
        }

        public function enable_checkbox() {
            $this->checkbox = true;
            return $this;
        }

        public function disable_checkbox() {
            $this->checkbox = false;
            return $this;
        }

        public function set_query($q) {
            $this->query = $q;
            return $this;
        }

        public function set_data_from_query($q) {
            $db = $this->db;
            $r = $db->query($q)->result(false);
            $this->data = $r;
            $this->query = $q;
            return $this;
        }

        public function set_data_from_array($a) {
            $this->data = $a;
            return $this;
        }

        public function set_pdf_font_size($size) {
            $this->pdf_font_size = $size;
            return $this;
        }

        public function set_pdf_orientation($orientation) {

            if (strtoupper($orientation) == "PORTRAIT") $orientation = "P";
            if (strtoupper($orientation) == "LANDSCAPE") $orientation = "L";
            if (!in_array($orientation, array("L", "P"))) $orientation = "P";

            $this->pdf_orientation = $orientation;
            return $this;
        }

        public function export_pdf($filename) {
            $this->export_pdf = true;
            $html = $this->html();
            $p = new CPDFTable($this->pdf_orientation);
            $p->setfont('times', '', $this->pdf_font_size);
            $p->htmltable($html, 1);

            $p->output('', 'I');

            die();
        }

        public function export_excelcsv($filename) {
            $this->export_excelcsv = true;
            $csv_field_terminated = ",";
            $csv_field_enclosed = "\"";
            $csv_field_escaped = "\"\"";
            $csv_line_terminated = "\r\n";
            $csv_header = true;
            $csv_header_uppercase = true;
            $csv_header_field_terminated = ",";
            $csv_header_line_terminated = "\r\n";
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=" . $filename);
            if ($csv_header) {
                $line_header = "";
                if ($this->numbering) {
                    if (strlen($line_header) > 0)
                            $line_header.=$csv_header_field_terminated;
                    $field = 'No';
                    if ($csv_header_uppercase) $field = strtoupper($field);
                    $line_header .= $field;
                }
                foreach ($this->columns as $col) {
                    if (strlen($line_header) > 0)
                            $line_header.=$csv_header_field_terminated;
                    $field = $col->get_label();
                    if ($csv_header_uppercase) $field = strtoupper($field);
                    $line_header .= $field;
                }
                echo $line_header . $csv_header_line_terminated;
            }
            $data = $this->data;
            $no = 0;
            if (is_object($data)) $data = $data->result_array(false);
            foreach ($data as $row) {
                $no++;
                $key = "";
                $line = "";
                if (array_key_exists($this->key_field, $row)) {

                    $key = $row[$this->key_field];
                }
                $class = "";
                if ($this->numbering) {
                    if (strlen($line) > 0) $line.=$csv_field_terminated;
                    $field = $no;
                    if (strlen($csv_field_escaped) > 0) {
                        $field = str_replace($csv_field_enclosed, $csv_field_escaped, $field);
                    }
                    if (strlen($csv_field_enclosed) > 0) {
                        $field = $csv_field_enclosed . $field . $csv_field_enclosed;
                    }
                    $line.=$field;
                }

                $jsparam = array();
                foreach ($this->columns as $col) {
                    $col_found = false;
                    $new_v = "";
                    $col_v = "";
                    $ori_v = "";
                    //do print from query
                    foreach ($row as $k => $v) {
                        if ($k == $col->get_fieldname()) {
                            $col_v = $v;
                            $ori_v = $col_v;
                            foreach ($col->transforms as $trans) {
                                $col_v = $trans->execute($col_v);
                            }
                        }
                    }
                    //if formatted
                    if (strlen($col->format) > 0) {
                        $temp_v = $col->format;
                        foreach ($row as $k2 => $v2) {

                            if (strpos($temp_v, "{" . $k2 . "}") !== false) {

                                $temp_v = str_replace("{" . $k2 . "}", $v2, $temp_v);
                            }
                            $col_v = $temp_v;
                        }
                    }

                    $new_v = $col_v;

                    if (($this->cell_callback_func) != null) {
                        $new_v = CDynFunction::factory($this->cell_callback_func)
                                ->add_param($this)
                                ->add_param($col->get_fieldname())
                                ->add_param($row)
                                ->add_param($new_v)
                                ->set_require($this->requires)
                                ->execute();


                        //call_user_func($this->cell_callback_func,$this,$col->get_fieldname(),$row,$v);
                    }
                    $class = "";
                    if (strlen($line) > 0) $line.=$csv_field_terminated;
                    $field = $new_v;
                    if (strlen($csv_field_escaped) > 0) {
                        $field = str_replace($csv_field_enclosed, $csv_field_escaped, $field);
                    }
                    if (strlen($csv_field_enclosed) > 0) {
                        $field = $csv_field_enclosed . $field . $csv_field_enclosed;
                    }
                    $line.=$field;



                    $col_found = true;
                }


                echo $line . $csv_line_terminated;
            }
            exit;
        }

        public function export_excelxml($filename, $sheet_name = null) {
            $this->export_excelxml = true;
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=" . $filename);
            echo '
                <html xmlns:o="urn:schemas-microsoft-com:office:office"
                        xmlns:x="urn:schemas-microsoft-com:office:excel"
                        xmlns="http://www.w3.org/TR/REC-html40">
                        <head>
                        <meta http-equiv=Content-Type content="text/html; charset=us-ascii">
                        </meta><meta name=ProgId content=Excel.Sheet>
                        </meta><meta name=Generator content="Microsoft Excel 11">
                        <link rel=File-List href="data.xls_files/filelist.xml">
                        </link><link rel=Edit-Time-Data href="data.xls_files/editdata.mso">
                        </link><link rel=OLE-Object-Data href="data.xls_files/oledata.mso">
                        </link></meta>
                        <style>

                        table {
                            mso-displayed-decimal-separator:"\.";
                            mso-displayed-thousand-separator:"\,";
                        }
                        col{
                            mso-width-source:auto;
                        }
                        td{
                                font-size:8pt;
                                font-family:Arial;

                        }
                        th{
                            font-size:8pt;font-family:Arial;
                        }
                        table thead th {
                             background:#000;
                            border-top:.5pt solid #545454;
                            border-left:.5pt solid #545454;
                            border-right:.5pt solid #545454;
                            border-bottom:.5pt solid #545454;
                            color:#fff;
                        }
                        @Page{
                            mso-header-data:"&L&BInternational&B&C&BAsia Pacific&B&R&BPage &P&B";
                            mso-footer-data:"&L&022Arial022Asia Pacific&R2011-09-08";
                            margin:1.0in .75in 1.0in .75in;
                            mso-header-margin:.5in;
                            mso-footer-margin:.5in;
                        }
                       

                        .tfoot{
                          background:#000;
                          border-top:.5pt solid #545454;
                          border-left:.5pt solid #545454;
                          border-right:.5pt solid #545454;
                          border-bottom:.5pt solid #545454;
                          color:#fff;
                          font-weight:bold;
                        }



                        .odd{background:#f7f7f7;}
                        .even{background:#e7e7e7;}

                        table.data td {
                                border-top:.5pt solid #545454;
                                border-left:.5pt solid #545454;
                                border-right:.5pt solid #545454;
                                border-bottom:.5pt solid #545454;
                        }



                        .align-left {text-align:left;}
                        .align-right {text-align:right;}
                        .align-center {text-align:center;}

                        </style>
                        </head>
                        <body>';
            echo '<table class="data table table-bordered table-striped responsive" id="' . $this->id . '">';
            echo '<thead>';

            $header_count = count($this->report_header);
            $total_column = count($this->columns);
            $addition_column = 0;
            if ($this->numbering) $addition_column++;
            for ($ii = 1; $ii <= $header_count; $ii++) {
                echo '<tr><td colspan="' . ($total_column + $addition_column) . '">' . $this->report_header[$ii - 1] . '</td></tr>';
            }
            if (strlen($this->custom_column_header) > 0) {
                echo $this->custom_column_header;
            }
            else {
                echo '<tr>';

                if ($this->numbering) {
                    echo '<th class="align-right thead" data-align="align-right" width="20" scope="col">No</th>';
                }

                foreach ($this->columns as $col) {
                    echo $col->render_header_html($this->export_pdf, '', 0);
                }
                echo '</tr>';
            }
            echo '</thead>';
            echo '<tbody>';
            $no = 0;
            $data = $this->data;
            if (is_object($data)) $data = $data->result_array(false);
            foreach ($data as $row) {
                $no++;
                $key = "";

                if (array_key_exists($this->key_field, $row)) {

                    $key = $row[$this->key_field];
                }
                $class = "";
                if ($no % 2 == 0) {
                    $class.=" even";
                }
                else {
                    $class.=" odd";
                }
                echo '<tr class="' . $class . '" id="tr-' . $key . '">';


                if ($this->numbering) {
                    echo '<td scope="row" class="align-right">' . $no . '</td>';
                }

                $jsparam = array();
                foreach ($this->columns as $col) {
                    $col_found = false;
                    $new_v = "";
                    $col_v = "";
                    $ori_v = "";
                    //do print from query
                    foreach ($row as $k => $v) {
                        if ($k == $col->get_fieldname()) {
                            $col_v = $v;
                            $ori_v = $col_v;
                            foreach ($col->transforms as $trans) {
                                $col_v = $trans->execute($col_v);
                            }
                        }
                    }
                    //if formatted
                    if (strlen($col->format) > 0) {
                        $temp_v = $col->format;
                        foreach ($row as $k2 => $v2) {

                            if (strpos($temp_v, "{" . $k2 . "}") !== false) {

                                $temp_v = str_replace("{" . $k2 . "}", $v2, $temp_v);
                            }
                            $col_v = $temp_v;
                        }
                    }

                    $new_v = $col_v;

                    if (($this->cell_callback_func) != null) {
                        $new_v = CDynFunction::factory($this->cell_callback_func)
                                ->add_param($this)
                                ->add_param($col->get_fieldname())
                                ->add_param($row)
                                ->add_param($new_v)
                                ->set_require($this->requires)
                                ->execute();


                        //call_user_func($this->cell_callback_func,$this,$col->get_fieldname(),$row,$v);
                    }
                    $class = "";
                    switch ($col->get_align()) {
                        case "left": $class.=" align-left";
                            break;
                        case "right": $class.=" align-right";
                            break;
                        case "center": $class.=" align-center";
                            break;
                    }
                    if ($no % 2 == 0) {
                        $class.=" even";
                    }
                    else {
                        $class.=" odd";
                    }
                    echo '<td class="' . $class . '" data-column="' . $col->get_fieldname() . '">' . $new_v . '</td>';

                    $col_found = true;
                }
                echo '</tr>';
            }
            echo '</tbody>';
            if ($this->footer) {
                echo '<tfoot>';

                $total_column = count($this->columns);
                $addition_column = 0;
                if ($this->numbering) $addition_column++;

                foreach ($this->footer_field as $f) {
                    echo '<tr>';

                    $colspan = $f["labelcolspan"];
                    if ($colspan == 0)
                            $colspan = $total_column + $addition_column - 1;
                    echo '<td class="tfoot" colspan="' . ($colspan) . '">';
                    echo $f["label"];
                    echo '</td>';
                    $class = "";
                    switch ($f["align"]) {
                        case "left": $class.=" align-left";
                            break;
                        case "right": $class.=" align-right";
                            break;
                        case "center": $class.=" align-center";
                            break;
                    }

                    $fval = $f["value"];

                    if (is_array($fval)) {
                        $skip_column = 0;

                        foreach ($this->columns as $col) {
                            $is_skipped = false;
                            if ($skip_column < $colspan) {
                                $skip_column++;
                                $is_skipped = true;
                            }
                            if (!$is_skipped) {
                                $fcolval = "";
                                if (isset($fval[$col->get_fieldname()])) {
                                    $fcolval = $fval[$col->get_fieldname()];
                                }

                                switch ($col->get_align()) {
                                    case "left": $class.=" align-left";
                                        break;
                                    case "right": $class.=" align-right";
                                        break;
                                    case "center": $class.=" align-center";
                                        break;
                                }
                                echo '<td class="tfoot ' . $class . '">';
                                echo $fcolval;
                                echo '</td>';
                            }
                        }
                    }
                    else {
                        echo '<td class="tfoot ' . $class . '">';
                        echo '$fval';
                        echo '</td>';
                    }
                    echo '</tr>';
                }
                echo '</tfoot>';
            }
            echo '</table>';
            echo '</body>';
            echo '</html>';
            exit;
        }

        public function add_report_header($line) {
            $this->report_header[] = $line;
            return $this;
        }

        public function export_excel($filename, $sheet_name) {
            $this->export_excel = true;
            $excel = CExcel::factory()->set_creator("cresenity_system")->set_subject("Cresenity Report");
            $excel->set_active_sheet_name($sheet_name);
            $header_count = count($this->report_header);

            $total_column = count($this->columns);
            $addition_column = 0;
            if ($this->numbering) $addition_column++;
            if ($total_column < 2) $total_column = 2;



            $total_column += $addition_column - 1;


            for ($ii = 1; $ii <= $header_count; $ii++) {

                $excel->write_by_index(0, $ii, $this->report_header[$ii - 1]);
                $excel->merge_cell(0, $ii, $total_column, $ii);
            }

            $i = 0;
            if ($this->numbering) {
                $excel->write_by_index($i, $header_count + 1, "No");
                $i++;
            }
            foreach ($this->columns as $col) {
                $excel->write_by_index($i, $header_count + 1, $col->label);
                $i++;
            }




            $i = 0;
            $j = 2 + $header_count;
            $no = 0;
            foreach ($this->data as $row) {
                $i = 0;
                $no++;
                $key = $row[$this->key_field];

                if ($this->numbering) {
                    $excel->write_by_index($i, $j, $no);
                    $i++;
                }


                foreach ($this->columns as $col) {
                    $col_found = false;
                    $new_v = "";
                    $col_v = "";
                    //do print from query
                    foreach ($row as $k => $v) {
                        if ($k == $col->get_fieldname()) {
                            $col_v = $v;

                            foreach ($col->transforms as $trans) {
                                $col_v = $trans->execute($col_v);
                            }
                        }
                    }
                    //if formatted
                    if (strlen($col->format) > 0) {
                        $temp_v = $col->format;
                        foreach ($row as $k2 => $v2) {

                            if (strpos($temp_v, "{" . $k2 . "}") !== false) {

                                $temp_v = str_replace("{" . $k2 . "}", $v2, $temp_v);
                            }
                            $col_v = $temp_v;
                        }
                    }

                    $new_v = $col_v;

                    if (($this->cell_callback_func) != null) {
                        $new_v = CDynFunction::factory($this->cell_callback_func)
                                ->add_param($this)
                                ->add_param($col->get_fieldname())
                                ->add_param($row)
                                ->add_param($new_v)
                                ->set_require($this->requires)
                                ->execute();


                        //call_user_func($this->cell_callback_func,$this,$col->get_fieldname(),$row,$v);
                    }
                    $class = "";
                    switch ($col->get_align()) {

                        case "left": $class.=" align-left";
                            break;
                        case "right": $class.=" align-right";
                            break;
                        case "center": $class.=" align-center";
                            break;
                    }
                    //parse new_v
                    $ss = array();
                    while (preg_match('/<a.+?>(.+?)<\/a>/', $new_v, $ss)) {
                        $new_v = str_replace($ss[0], $ss[1], $new_v);
                    }


                    $excel->write_by_index($i, $j, $new_v);
                    $excel->set_align_by_index($i, $j, $col->get_align());

                    $i++;
                    $col_found = true;
                }
                $j++;
            }
            //footer


            if ($this->footer) {
                $total_column = count($this->columns);
                $addition_column = 0;
                if ($this->numbering) $addition_column++;
                if ($total_column < 2) $total_column = 2;
                foreach ($this->footer_field as $f) {

                    $colspan = $f["labelcolspan"];
                    if ($colspan == 0)
                            $colspan = $total_column + $addition_column - 1;

                    $excel->write_by_index(0, $j, $f["label"] . $colspan);
                    $excel->set_align_by_index(0, $j, "left");
                    $fval = $f["value"];

                    if (is_array($fval)) {
                        $skip_column = 0;
                        $i = 0;

                        foreach ($this->columns as $col) {
                            $is_skipped = false;

                            if ($skip_column < $colspan) {
                                $skip_column++;
                                $is_skipped = true;
                            }
                            if (!$is_skipped) {
                                $fcolval = "";
                                if (isset($fval[$col->get_fieldname()])) {
                                    $fcolval = $fval[$col->get_fieldname()];
                                }

                                switch ($col->get_align()) {
                                    case "left": $class.=" align-left";
                                        break;
                                    case "right": $class.=" align-right";
                                        break;
                                    case "center": $class.=" align-center";
                                        break;
                                }
                                $excel->write_by_index($i, $j, $fcolval);
                                $excel->set_align_by_index($i, $j, $col->get_align());
                            }
                            $i++;
                        }
                    }
                    else {
                        $excel->write_by_index($total_column - 1, $j, $fval);
                        $excel->set_align_by_index($total_column - 1, $j, $f["align"]);
                    }
                    if ($colspan > 1) {
                        $excel->merge_cell(0, $j, $colspan - 1, $j);
                    }
                    $excel->set_row_style($j);

                    $j++;
                }
            }
            $excel->set_auto_width();
            $excel->set_header_style($header_count + 1);
            $sfn = cstr::sanitize($filename, true);

            $fn = cexport::makepath("excel", $sfn);
            $excel->save($fn);
            //echo $fn;

            cdownload::force($fn, null, $sfn);
        }

        public function html($indent = 0) {
            $pdf_table_attr = '';
            $pdf_tbody_td_attr = '';
            $pdf_thead_td_attr = '';

            if ($this->export_pdf) {
                $pdf_table_attr = ' border="1" width="100%"';
                $pdf_thead_td_attr = ' bgcolor="#9f9f9f" color="#000" style="color:#000" ';
                $pdf_tbody_td_attr = ' valign="middle"';
            }
            $th_class = "";
            if ($this->header_no_line_break) {
                $th_class = " no-line-break";
            }
            $html = new CStringBuilder();
            $html->set_indent($indent);
            $wrapped = ($this->apply_data_table > 0) || $this->have_header_action();
            if ($wrapped) {
                $html->appendln('<div class="widget-box widget-table">')->inc_indent();
                $html->appendln('<div class="widget-title">')->inc_indent();
                if (strlen($this->icon > 0)) {
                    $html->appendln('<span class="icon">')->inc_indent();
                    $html->appendln('<i class="icon-' . $this->icon . '"></i>');
                    $html->dec_indent()->appendln('</span');
                }
                $html->appendln('<h5>' . $this->title . '</h5>');
                if ($this->have_header_action()) {
                    $html->appendln($this->header_action_list->html($html->get_indent()));
                }
                $html->dec_indent()->appendln('</div>');
                $html->appendln('<div class="widget-content nopadding">')->inc_indent();
            }
            $data_responsive_open = '';
            $data_responsive_close = '';
            if ($this->responsive) {
                $data_responsive_open = '<div class="span12" style="overflow: auto;margin-left: 0;">';
                $data_responsive_close = '</div>';
            }
            $html->appendln($data_responsive_open . '<table ' . $pdf_table_attr . ' class="table table-bordered table-striped responsive" id="' . $this->id . '">')
                    ->inc_indent()->br();
            if ($this->show_header) {
                $html->appendln('<thead>')
                        ->inc_indent()->br();
                if (strlen($this->custom_column_header) > 0) {
                    $html->appendln($this->custom_column_header);
                }
                else {
                    $html->appendln('<tr>')
                            ->inc_indent()->br();

                    if ($this->numbering) {
                        $html->appendln('<th data-align="align-right" class="' . $th_class . '" width="20" scope="col">No</th>')->br();
                    }
                    if ($this->checkbox) {
                        $html->appendln('<th data-align="align-center" class="' . $th_class . '" scope="col"><input type="checkbox" name="' . $this->id . '-check-all" id="' . $this->id . '-check-all" value="1"></th>')->br();
                    }
                    foreach ($this->columns as $col) {
                        $html->appendln($col->render_header_html($this->export_pdf, $th_class, $html->get_indent()))->br();
                    }
                    if ($this->have_action()) {
                        $action_width = 31 * $this->action_count() + 5;
                        if ($this->action_style == "btn-dropdown") {
                            $action_width = 70;
                        }
                        $html->appendln('<th data-align="align-center" scope="col" width="' . $action_width . '" class="align-center' . $th_class . '">' . clang::__('Actions') . '</th>')->br();
                    }
                    $html->dec_indent()->appendln("</tr>")->br();
                }
                $html->dec_indent()->appendln("</thead>")->br();
            }

            $tbody_id = (strlen($this->tbody_id) > 0 ? "id='" . $this->tbody_id . "' " : "");

            $html->appendln("<tbody " . $tbody_id . " >" . $data_responsive_close)->inc_indent()->br();
            //render body;
            $no = 0;
            if (!$this->ajax) {
                foreach ($this->data as $row) {
                    if ($row instanceof CRenderable) {
                        $html->appendln($row->html());
                        continue;
                    }

                    $no++;
                    $key = "";

                    if (array_key_exists($this->key_field, $row)) {

                        $key = $row[$this->key_field];
                    }
                    $html->appendln('<tr id="tr-' . $key . '">')->inc_indent()->br();

                    if ($this->numbering) {
                        $html->appendln('<td scope="row" class="align-right">' . $no . '</td>')->br();
                    }
                    if ($this->checkbox) {
                        $checkbox_checked = "";
                        if (in_array($key, $this->checkbox_value)) {
                            $checkbox_checked = ' checked="checked"';
                        }
                        $html->appendln('<td scope="row" class="checkbox-cell align-center"><input type="checkbox" class="checkbox-' . $this->id . '" name="' . $this->id . '-check[]" id="' . $this->id . '-' . $key . '" value="' . $key . '"' . $checkbox_checked . '></td>')->br();
                    }
                    $jsparam = array();
                    foreach ($this->columns as $col) {
                        $col_found = false;
                        $new_v = "";
                        $col_v = "";
                        $ori_v = "";
                        //do print from query
                        foreach ($row as $k => $v) {
                            if ($v instanceof CRenderable) {
                                $v = $v->html();
                            }
                            if ($k == $col->get_fieldname()) {
                                $col_v = $v;
                                $ori_v = $col_v;
                                foreach ($col->transforms as $trans) {
                                    $col_v = $trans->execute($col_v);
                                }
                            }
                        }
                        //if formatted
                        if (strlen($col->format) > 0) {
                            $temp_v = $col->format;
                            foreach ($row as $k2 => $v2) {

                                if (strpos($temp_v, "{" . $k2 . "}") !== false) {

                                    $temp_v = str_replace("{" . $k2 . "}", $v2, $temp_v);
                                }
                                $col_v = $temp_v;
                            }
                        }

                        $new_v = $col_v;

                        if (($this->cell_callback_func) != null) {
                            $new_v = CDynFunction::factory($this->cell_callback_func)
                                    ->add_param($this)
                                    ->add_param($col->get_fieldname())
                                    ->add_param($row)
                                    ->add_param($new_v)
                                    ->set_require($this->requires)
                                    ->execute();


                            //call_user_func($this->cell_callback_func,$this,$col->get_fieldname(),$row,$v);
                        }
                        $class = "";
                        switch ($col->get_align()) {
                            case "left": $class.=" align-left";
                                break;
                            case "right": $class.=" align-right";
                                break;
                            case "center": $class.=" align-center";
                                break;
                        }
                        if ($col->get_no_line_break()) {
                            $class.=" no-line-break";
                        }
                        if ($col->hidden_phone) $class.=" hidden-phone";

                        if ($col->hidden_tablet) $class.=" hidden-tablet";

                        if ($col->hidden_desktop) $class.=" hidden-desktop";

                        $pdf_tbody_td_current_attr = $pdf_tbody_td_attr;
                        if ($this->export_pdf) {
                            switch ($col->get_align()) {
                                case "left": $pdf_tbody_td_current_attr.=' align="left"';
                                    break;
                                case "right": $pdf_tbody_td_current_attr.=' align="right"';
                                    break;
                                case "center": $pdf_tbody_td_current_attr.=' align="center"';
                                    break;
                            }
                        }
                        $html->appendln('<td' . $pdf_tbody_td_current_attr . ' class="' . $class . '" data-column="' . $col->get_fieldname() . '">' . $new_v . '</td>')->br();
                        $col_found = true;
                    }

                    if ($this->have_action()) {
                        $html->appendln('<td class="low-padding align-center">')->inc_indent()->br();
                        foreach ($row as $k => $v) {
                            $jsparam[$k] = $v;
                        }

                        $jsparam["param1"] = $key;
                        if ($this->action_style == "btn-dropdown") {
                            $this->row_action_list->add_class("pull-right");
                        }
                        $this->row_action_list->regenerate_id(true);
                        $this->row_action_list->apply("jsparam", $jsparam);
                        $this->row_action_list->apply("set_handler_url_param", $jsparam);
                        $this->js_cell.=$this->row_action_list->js();

                        $html->appendln($this->row_action_list->html($html->get_indent()));
                        $html->dec_indent()->appendln('</td>')->br();
                    }



                    $html->dec_indent()->appendln('</tr>')->br();
                }
            }


            $html->dec_indent()->appendln('</tbody>')->br();
            //footer
            if ($this->footer) {
                $html->inc_indent()->appendln('<tfoot>')->br();
                $total_column = count($this->columns);
                $addition_column = 0;
                if ($this->have_action()) $addition_column++;
                if ($this->numbering) $addition_column++;
                if ($this->checkbox) $addition_column++;

                foreach ($this->footer_field as $f) {
                    $html->inc_indent()->appendln('<tr>')->br();
                    $colspan = $f["labelcolspan"];
                    if ($colspan == 0)
                            $colspan = $total_column + $addition_column - 1;
                    $html->inc_indent()->appendln('<td colspan="' . ($colspan) . '">')->br();
                    $html->appendln($f["label"])->br();
                    $html->dec_indent()->appendln('</td>')->br();
                    $class = "";
                    switch ($f["align"]) {
                        case "left": $class.=" align-left";
                            break;
                        case "right": $class.=" align-right";
                            break;
                        case "center": $class.=" align-center";
                            break;
                    }

                    $fval = $f["value"];
                    if ($fval instanceof CRenderable) {
                        $html->inc_indent()->appendln('<td class="' . $class . '">')->br();
                        $html->appendln($fval->html($indent))->br();
                        $html->dec_indent()->appendln('</td>')->br();
                    }
                    else if (is_array($fval)) {
                        $skip_column = 0;

                        foreach ($this->columns as $col) {
                            $is_skipped = false;
                            if ($skip_column < $colspan) {
                                $skip_column++;
                                $is_skipped = true;
                            }
                            if (!$is_skipped) {
                                $fcolval = "";
                                if (isset($fval[$col->get_fieldname()])) {
                                    $fcolval = $fval[$col->get_fieldname()];
                                }

                                switch ($col->get_align()) {
                                    case "left": $class.=" align-left";
                                        break;
                                    case "right": $class.=" align-right";
                                        break;
                                    case "center": $class.=" align-center";
                                        break;
                                }
                                $html->inc_indent()->appendln('<td class="' . $class . '">')->br();
                                $html->appendln($fcolval)->br();
                                $html->dec_indent()->appendln('</td>')->br();
                            }
                        }
                    }
                    else {
                        $html->inc_indent()->appendln('<td class="' . $class . '">')->br();
                        $html->appendln($fval)->br();
                        $html->dec_indent()->appendln('</td>')->br();
                    }
                    $html->dec_indent()->appendln('</tr>')->br();
                }
                $html->dec_indent()->appendln('</tfoot>')->br();
            }
            $html->dec_indent()
                    ->appendln('</table>');
            if ($wrapped > 0) {
                $html->dec_indent()->appendln('</div>');
                $html->dec_indent()->appendln('</div>');
            }




            /*
              if($this->have_action()) {
              $modal = CDialog::factory($this->id.'_modal')->set_title('Role');
              $form = CForm::factory($this->id.'_form');
              foreach($this->columns as $col) {
              if($col->visible) {
              $form->add_field($col->fieldname."_field")->set_label($col->label)->add_control($col->fieldname,$col->input_type)->set_disabled($col->editable==false);
              }
              }

              $actions = CActionList::factory($this->id.'_form_actions');
              $act_prev = CAction::factory($this->id.'_form_close')->set_label('Prev')->set_link(curl::base().'cresenity/install/');
              $act_next = CAction::factory($this->id.'_form_submit')->set_label('Next')->set_submit(true);
              $actions->add($act_prev);
              $actions->add($act_next);
              $actions->set_style('form-action');
              $form->add($actions);
              $modal->add($form);
              $html->append($modal->html($html->get_indent()));
              }
             */




            return $html->text();
        }

        public function js($indent = 0) {
            $ajax_url = "";
            if ($this->ajax) {
                $columns = array();
                foreach ($this->columns as $col) {
                    $columns[] = $col;
                }
                $ajax_url = CAjaxMethod::factory()->set_type('datatable')
                        ->set_data('columns', $columns)
                        ->set_data('query', $this->query)
                        ->set_data('row_action_list', $this->row_action_list)
                        ->set_data('key_field', $this->key_field)
                        ->set_data('table', serialize($this))
                        ->makeurl();
            }
            $js = new CStringBuilder();
            $js->set_indent($indent);


            $total_column = count($this->columns);
            if (count($this->row_action_list->child_count()) > 0) {
                $total_column++;
            }
            if ($this->checkbox) {
                $total_column++;
            }


            if ($this->apply_data_table > 0) {

                $length_menu = "";
                $km = "";
                $vm = "";
                foreach ($this->paging_list as $k => $v) {
                    if (strlen($km) > 0) $km.=", ";
                    if (strlen($vm) > 0) $vm.=", ";
                    $km .= $k;
                    $vm .= "'" . $v . "'";
                }
                $hs_val = $this->header_sortable ? "true" : "false";
                $js->appendln("var table = jQuery('#" . $this->id . "');")->br();
                $js->appendln("var header_sortable = " . $hs_val . ";")->br();
                $js->appendln("var vaoColumns = [];")->br();
                if ($this->numbering) {
                    $aojson = array();
                    $aojson["bSortable"] = false;
                    $aojson["bSearchable"] = false;
                    $aojson["bVisible"] = true;
                    $js->appendln("vaoColumns.push( " . json_encode($aojson) . " );")->br();
                    ;
                }
                if ($this->checkbox) {
                    $aojson = array();
                    $aojson["bSortable"] = false;
                    $aojson["bSearchable"] = false;
                    $aojson["bVisible"] = true;
                    $js->appendln("vaoColumns.push( " . json_encode($aojson) . " );")->br();
                    ;
                }

                foreach ($this->columns as $col) {
                    $aojson = array();
                    $aojson["bSortable"] = $col->sortable && $this->header_sortable;
                    $aojson["bSearchable"] = $col->searchable;
                    $aojson["bVisible"] = $col->visible;

                    $js->appendln("
                            
					vaoColumns.push( " . json_encode($aojson) . " );");
                }
                if ($this->have_action()) {
                    $aojson = array();
                    $aojson["bSortable"] = false;
                    $aojson["bSearchable"] = false;
                    $aojson["bVisible"] = true;
                    $js->appendln("vaoColumns.push( " . json_encode($aojson) . " );")->br();
                }



                $js->appendln("var tableStyled_" . $this->id . " = false;")->br()->
                        appendln("var oTable = table.dataTable({")->br()->inc_indent();

                if ($this->ajax) {
                    $js->append("")
                            ->appendln("'bRetrieve': true,")->br()
                            ->appendln("'bProcessing': true,")->br()
                            ->appendln("'bServerSide': true,")->br()
                            ->appendln("'sAjaxSource': '" . $ajax_url . "',")->br()
                            ->appendln("'sServerMethod': '" . strtoupper($this->ajax_method) . "',")->br()
                            ->appendln("'fnServerData': function ( sSource, aoData, fnCallback, oSettings ) {
                                            var data_quick_search = [];
                                            jQuery('.data_table-quick_search').each(function(){
                                                if (jQuery(this).val() != '') {
                                                    var input_name = jQuery(this).attr('name');
                                                    var cur_transforms = jQuery(this).attr('transforms');
                                                    data_quick_search.push({'name': input_name, 'value': jQuery(this).val(), 'transforms': cur_transforms});
                                                }
                                            });
                                            aoData.push({'name': 'dttable_quick_search', 'value': JSON.stringify(data_quick_search)});
                                            oSettings.jqXHR = $.ajax( {
                                                    'dataType': 'json',
                                                    'type': '" . strtoupper($this->ajax_method) . "',
                                                    'url': sSource,
                                                    'data': aoData,
                                                    'success': function(data) {
                                                            fnCallback(data.datatable);
                                                            var script = $.cresenity.base64.decode(data.js);
                                                            eval(script);

                                                    },
                                                    'error': function(a,b,c) {
                                                            $.cresenity.message(a);
                                                    }
                                            })
                                        },
                                    ")
                            ->appendln("'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
						// Bold the grade for all 'A' grade browsers
						
							//$('td:eq(4)', nRow).html( '<b>A</b>' );
						//$.cresenity.set_confirm($('a.confirm',nRow));
						
						var footer_action = $('#footer_action_" . $this->id . "');
						
						" . ($this->have_footer_action() ? "footer_action.html(" . json_encode($this->footer_action_list->html()) . ");" : "") . " 
           
						" . ($this->have_footer_action() ? "" . $this->footer_action_list->js() . "" : "") . " 
						
						footer_action.css('position','absolute').css('left','275px').css('margin','4px 8px 2px 10px');
						
						for(i=0;i<$(nRow).find('td').length;i++) {
							
							//get head data align
							var data_align = $('#" . $this->id . "').find('thead th:eq('+i+')').data('align');
							var data_no_line_break = $('#" . $this->id . "').find('thead th:eq('+i+')').data('no-line-break');
							if(data_align) {
								$('td:eq('+i+')', nRow).addClass(data_align);
							}
							if(data_no_line_break) {
								$('td:eq('+i+')', nRow).addClass(data_no_line_break);
							}
						}
						
						
					},
				")
                            ->appendln("'fnInitComplete': function() {
					this.fnAdjustColumnSizing(true);
					},
				")
                    ;
                }
                /*
                  $js->append("")
                  ->appendln("'sScrollX': '100%',")->br()
                  ->appendln("'bScrollCollapse': true,")->br()
                  ;
                 */
                $js->append("")
                        ->appendln("'oLanguage': {
						'sLoadingRecords': '" . clang::__('Loading') . "',
						'sZeroRecords': '" . clang::__('No records to display') . "',
						'oPaginate': {
							'sFirst': '" . clang::__('First') . "',
							'sPrevious': '" . clang::__('Previous') . "',
							'sNext': '" . clang::__('Next') . "',
							'sLast': '" . clang::__('Last') . "'
							
						}
					},")->br()
                        ->appendln("'bJQueryUI': true,")->br()
                        ->appendln("'bStateSave': false,")->br()
                        ->appendln("'iDisplayLength': " . $this->display_length . ",")->br()
                        ->appendln("'bSortCellsTop': " . $hs_val . ",")->br()
                        ->appendln("'aaSorting': [],")->br()
                        ->appendln("'oLanguage': { 
						sSearch : '" . clang::__('Search') . "',
						sProcessing : '" . clang::__('Processing') . "',
						sLengthMenu  : '" . clang::__('Show') . " _MENU_ " . clang::__('Entries') . "',
						oPaginate  : {'sFirst' : '" . clang::__('First') . "',
										'sLast' : '" . clang::__('Last') . "',
										'sNext' : '" . clang::__('Next') . "',
										'sPrevious' : '" . clang::__('Previous	') . "'},
						sInfoEmpty  : '" . clang::__('No data available in table') . "',
						sEmptyTable  : '" . clang::__('No data available in table') . "',
						sInfoThousands   : '" . clang::__('') . "',
					},")->br()
                        ->appendln("'bDeferRender': " . ($this->get_option("defer_render") ? "true" : "false") . ",")->br()
                        ->appendln("'bFilter': " . ($this->get_option("filter") ? "true" : "false") . ",")->br()
                        ->appendln("'bInfo': " . ($this->get_option("info") ? "true" : "false") . ",")->br()
                        ->appendln("'bPaginate': " . ($this->get_option("pagination") ? "true" : "false") . ",")->br()
                        ->appendln("'bLengthChange': " . ($this->get_option("length_change") ? "true" : "false") . ",")->br()
                        ->appendln("'aoColumns': vaoColumns,")->br()
                        ->appendln("'aLengthMenu': [
					[" . $km . "],
					[" . $vm . "]
				],")->br()
                ;

                /*
                  $js->append("")
                  ->appendln("'sScrollX': '100%',")->br()
                  ->appendln("'sScrollXInner': '100%',")->br()
                  ->appendln("'bScrollCollapse': true,")->br()
                  ;
                 */


                $js->append("")
                        ->appendln("'sPaginationType': 'full_numbers',")->br()
                        ->appendln("'sDom': '<\"\"l>t<\"F\"f<\"#footer_action_" . $this->id . "\">rp>',")->br()
                ;
                /*
                  $js->append("
                  'fnDrawCallback': function ( oSettings ) {");

                  if(strlen($this->group_by)>0) {
                  $col_ind = false;
                  $inc = 0;
                  foreach($this->columns as $col) {

                  if($col->get_fieldname()==$this->group_by) {
                  $col_ind=$inc;
                  break;
                  }
                  $inc++;
                  }

                  if($col_ind>=0) {
                  $js->appendln("
                  if ( oSettings.aiDisplay.length >= 0 ) {
                  var nTrs = $('#".$this->id." tbody tr');
                  var iColspan = nTrs[0].getElementsByTagName('td').length;
                  var sLastGroup = '';
                  for ( var i=0 ; i<nTrs.length ; i++ )
                  {
                  var iDisplayIndex = oSettings._iDisplayStart + i;
                  var sGroup = oSettings.aoData[ oSettings.aiDisplay[iDisplayIndex] ]._aData[".$col_ind."];
                  if ( sGroup != sLastGroup )
                  {
                  var nGroup = document.createElement( 'tr' );
                  var nCell = document.createElement( 'td' );
                  nCell.colSpan = iColspan;
                  nCell.className = 'group';
                  nCell.innerHTML = sGroup;
                  nGroup.appendChild( nCell );
                  nTrs[i].parentNode.insertBefore( nGroup, nTrs[i] );
                  sLastGroup = sGroup;
                  }
                  }
                  }
                  ");
                  }
                  }

                  $js->append("
                  },");
                 */

                $js->append("")
                        ->dec_indent()->appendln("});")->br();


//                $js->append("oTable.columns().every( function () {
//                                var that = this;
//
//                                $( 'input', this.footer() ).on( 'keyup change', function () {
//                                    that
//                                        .search( this.value )
//                                        .draw();
//                                } );
//                            } );");
                //$js->appendln("oTable.fnSortOnOff( '_all', false );")->br();

                $js->appendln('function buildFilters_' . $this->id . '() {')->br()
                        ->appendln("var quick_search = jQuery('<tr>');")->br()
                        ->appendln("jQuery('#" . $this->id . " thead th').each( function (i) {
                            var title = jQuery('#" . $this->id . " thead th').eq( jQuery(this).index() ).text();
                            var have_action = " . ($this->have_action() ? "1" : "0") . ";
                            
                           
                            var total_th = jQuery('#" . $this->id . " thead th').length;
                            var input = '';
                            if(!(have_action==1&&(total_th-1==jQuery(this).index()))) {
                                
                            
                                var all_column = " . json_encode($this->columns) . ";
                                var column = all_column[jQuery(this).index()];
                                var transforms = JSON.stringify(column.transforms);
                                if(column.searchable) {
                                    input = jQuery('<input>');
                                    input.attr('type', 'text');
                                    input.attr('name', 'dt_table_qs-' + jQuery(this).attr('field_name'));
                                    input.attr('class', 'data_table-quick_search');

                                    input.attr('transforms', transforms);
                                    input.attr('placeholder', 'Search ' + title );
                                    input.css('width','90%');
                                }
                                
                            }
                            var td = jQuery('<td>').append(input);
                            quick_search.append(td);
                        });")->br()
                        ->appendln("table.children('thead').append(quick_search);")->br()
                        ->appendln('}')->br()
                        ->appendln('var dttable_quick_search = ' . ($this->quick_search ? "1" : "0") . ';')->br()
                        ->appendln('if (dttable_quick_search == "1") { buildFilters_' . $this->id . '(); }')
                ;

                $js->appendln("jQuery('.data_table-quick_search').on('keyup', function(){
                            table.fnClearTable( 0 );
                            table.fnDraw();
                        });")
                ;
            }
            if ($this->checkbox) {
                $js->appendln("
				jQuery('#" . $this->id . "-check-all').click(function() {
					
					if(jQuery(this).is(':checked')) {
						
						jQuery('.checkbox-" . $this->id . "').attr('checked','checked');
					} else {
						jQuery('.checkbox-" . $this->id . "').removeAttr('checked');
					}
				});
				
			");
            }
            $js->appendln($this->js_cell);
            if (!$this->ajax) {
                foreach ($this->data as $row) {
                    if ($row instanceof CRenderable) {
                        $js->appendln($row->js())->br();
                        continue;
                    }
                    foreach ($row as $row_k => $row_v) {
                        if ($row_v instanceof CRenderable) {
                            $js->appendln($row_v->js())->br();
                        }
                    }
                }
            }

            if ($this->footer) {

                foreach ($this->footer_field as $f) {
                    $fval = $f["value"];
                    if ($fval instanceof CRenderable) {
                        $js->appendln($fval->js())->br();
                    }
                }
            }


//            clog::write($js->text());
            return $js->text();
        }

    }

?>