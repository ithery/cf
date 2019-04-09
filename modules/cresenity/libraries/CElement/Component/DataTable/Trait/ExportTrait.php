<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 3:48:03 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CElement_Component_DataTable_Trait_ExportTrait {

    public $export_xml;
    public $export_excel;
    public $export_excelxml;
    public $export_excelcsv;
    public $export_pdf;
    public $report_header = array();
    public $export_filename = '';
    public $export_sheetname = '';

    private static function exportExcelxmlStatic($filename, $sheet_name = null, $table) {


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
                        table.data th.thead{
                          background:#000;
                          border-top:.5pt solid #545454;
                          border-left:.5pt solid #545454;
                          border-right:.5pt solid #545454;
                          border-bottom:.5pt solid #545454;
                          color:#fff;
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
        echo '<table class="data table table-responsive table-bordered table-striped responsive" id="' . $table->id . '">';
        echo '<thead>';

        $header_count = count($table->report_header);
        $total_column = count($table->columns);
        $addition_column = 0;
        if ($table->numbering)
            $addition_column++;
        for ($ii = 1; $ii <= $header_count; $ii++) {
            echo '<tr><td colspan="' . ($total_column + $addition_column) . '">' . $table->report_header[$ii - 1] . '</td></tr>';
        }
        if (strlen($table->custom_column_header) > 0) {
            echo $table->custom_column_header;
        } else {
            echo '<tr>';

            if ($table->numbering) {
                echo '<th class="align-right thead" data-align="align-right" width="20" scope="col">No</th>';
            }

            foreach ($table->columns as $col) {
                echo $col->render_header_html($table->export_pdf, '', 0);
            }
            echo '</tr>';
        }
        echo '</thead>';
        echo '<tbody>';
        $no = 0;
        $data = $table->data;
        if (!is_resource($data)) {
            $data = CDatabase::instance()->query($table->query);
        }
        if (!is_resource($data)) {
            if (is_object($data))
                $data = $data->result_array(false);
        }
        foreach ($data as $row) {
            $no++;
            $key = "";

            if (array_key_exists($table->key_field, $row)) {

                $key = $row[$table->key_field];
            }
            $class = "";
            if ($no % 2 == 0) {
                $class .= " even";
            } else {
                $class .= " odd";
            }
            echo '<tr class="' . $class . '" id="tr-' . $key . '">';


            if ($table->numbering) {
                echo '<td scope="row" class="align-right">' . $no . '</td>';
            }

            $jsparam = array();
            foreach ($table->columns as $col) {
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

                if (($table->cell_callback_func) != null) {
                    $new_v = CDynFunction::factory($table->cell_callback_func)
                            ->add_param($table)
                            ->add_param($col->get_fieldname())
                            ->add_param($row)
                            ->add_param($new_v)
                            ->set_require($table->requires)
                            ->execute();

                    if (is_array($new_v) && isset($new_v['html']) && isset($new_v['js'])) {
                        $new_v = carr::get($new_v, 'html');
                        $js .= carr::get($new_v, 'js');
                    }
                    //call_user_func($table->cell_callback_func,$table,$col->get_fieldname(),$row,$v);
                }
                $class = "";
                switch ($col->get_align()) {
                    case "left": $class .= " align-left";
                        break;
                    case "right": $class .= " align-right";
                        break;
                    case "center": $class .= " align-center";
                        break;
                }
                if ($no % 2 == 0) {
                    $class .= " even";
                } else {
                    $class .= " odd";
                }
                echo '<td class="' . $class . '" data-column="' . $col->get_fieldname() . '">' . $new_v . '</td>';

                $col_found = true;
            }
            echo '</tr>';
        }
        echo '</tbody>';
        if ($table->footer) {
            echo '<tfoot>';

            $total_column = count($table->columns);
            $addition_column = 0;
            if ($table->numbering)
                $addition_column++;

            foreach ($table->footer_field as $f) {
                echo '<tr>';

                $colspan = $f["labelcolspan"];
                if ($colspan == 0)
                    $colspan = $total_column + $addition_column - 1;
                echo '<td class="tfoot" colspan="' . ($colspan) . '">';
                echo $f["label"];
                echo '</td>';
                $class = "";
                switch ($f["align"]) {
                    case "left": $class .= " align-left";
                        break;
                    case "right": $class .= " align-right";
                        break;
                    case "center": $class .= " align-center";
                        break;
                }

                $fval = $f["value"];

                if (is_array($fval)) {
                    $skip_column = 0;

                    foreach ($table->columns as $col) {
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
                                case "left": $class .= " align-left";
                                    break;
                                case "right": $class .= " align-right";
                                    break;
                                case "center": $class .= " align-center";
                                    break;
                            }
                            echo '<td class="tfoot ' . $class . '">';
                            echo $fcolval;
                            echo '</td>';
                        }
                    }
                } else {
                    echo '<td class="tfoot ' . $class . '">';
                    echo $fval;
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

    public function isExported() {
        return $this->export_excel || $this->export_excelxml || $this->export_excelcsv || $this->export_pdf;
    }

    public function setPdfOrientation($orientation) {

        if (strtoupper($orientation) == "PORTRAIT")
            $orientation = "P";
        if (strtoupper($orientation) == "LANDSCAPE")
            $orientation = "L";
        if (!in_array($orientation, array("L", "P")))
            $orientation = "P";

        $this->pdf_orientation = $orientation;
        return $this;
    }

    public function exportPdf($filename) {
        $this->export_pdf = true;
        $html = $this->html();
        $p = new CPDFTable($this->pdf_orientation);
        $p->setfont('times', '', $this->pdf_font_size);
        $p->htmltable($html, 1);

        $p->output('', 'I');

        die();
    }

    public function exportExcelcsv($filename) {
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
                    $line_header .= $csv_header_field_terminated;
                $field = 'No';
                if ($csv_header_uppercase)
                    $field = strtoupper($field);
                $line_header .= $field;
            }
            foreach ($this->columns as $col) {
                if (strlen($line_header) > 0)
                    $line_header .= $csv_header_field_terminated;
                $field = $col->get_label();
                if ($csv_header_uppercase)
                    $field = strtoupper($field);
                $line_header .= $field;
            }
            echo $line_header . $csv_header_line_terminated;
        }
        $data = $this->data;
        $no = 0;
        if (is_object($data))
            $data = $data->result_array(false);
        foreach ($data as $row) {
            $no++;
            $key = "";
            $line = "";
            if (array_key_exists($this->key_field, $row)) {

                $key = $row[$this->key_field];
            }
            $class = "";
            if ($this->numbering) {
                if (strlen($line) > 0)
                    $line .= $csv_field_terminated;
                $field = $no;
                if (strlen($csv_field_escaped) > 0) {
                    $field = str_replace($csv_field_enclosed, $csv_field_escaped, $field);
                }
                if (strlen($csv_field_enclosed) > 0) {
                    $field = $csv_field_enclosed . $field . $csv_field_enclosed;
                }
                $line .= $field;
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
                if (strlen($line) > 0)
                    $line .= $csv_field_terminated;
                $field = $new_v;
                if (strlen($csv_field_escaped) > 0) {
                    $field = str_replace($csv_field_enclosed, $csv_field_escaped, $field);
                }
                if (strlen($csv_field_enclosed) > 0) {
                    $field = $csv_field_enclosed . $field . $csv_field_enclosed;
                }
                $line .= $field;



                $col_found = true;
            }


            echo $line . $csv_line_terminated;
        }
        exit;
    }

    public function exportExcelxml($filename, $sheet_name = null) {
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
                        table.data th.thead{
                          background:#000;
                          border-top:.5pt solid #545454;
                          border-left:.5pt solid #545454;
                          border-right:.5pt solid #545454;
                          border-bottom:.5pt solid #545454;
                          color:#fff;
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
        if ($this->numbering)
            $addition_column++;
        for ($ii = 1; $ii <= $header_count; $ii++) {
            echo '<tr><td colspan="' . ($total_column + $addition_column) . '">' . $this->report_header[$ii - 1] . '</td></tr>';
        }
        if (strlen($this->custom_column_header) > 0) {
            echo $this->custom_column_header;
        } else {
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

        if (is_object($data)) {
            if ($data instanceof CDatabase_Driver_Mysqli_Result) {
                $data->setFetchTypeArray();
            } else {
                $data = $data->result_array(false);
            }
        }

        foreach ($data as $row) {
            $no++;
            $key = "";
            if (array_key_exists($this->key_field, $row)) {

                $key = $row[$this->key_field];
            }
            $class = "";
            if ($no % 2 == 0) {
                $class .= " even";
            } else {
                $class .= " odd";
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
                            if ($trans->get_function() != 'format_currency') {
                                $col_v = $trans->execute($col_v);
                            }
//                                $col_v = $trans->execute($col_v);
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
                    case "left": $class .= " align-left";
                        break;
                    case "right": $class .= " align-right";
                        break;
                    case "center": $class .= " align-center";
                        break;
                }
                if ($no % 2 == 0) {
                    $class .= " even";
                } else {
                    $class .= " odd";
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
            if ($this->numbering)
                $addition_column++;

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
                    case "left": $class .= " align-left";
                        break;
                    case "right": $class .= " align-right";
                        break;
                    case "center": $class .= " align-center";
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
                                case "left": $class .= " align-left";
                                    break;
                                case "right": $class .= " align-right";
                                    break;
                                case "center": $class .= " align-center";
                                    break;
                            }
                            echo '<td class="tfoot ' . $class . '">';
                            echo $fcolval;
                            echo '</td>';
                        }
                    }
                } else {
                    echo '<td class="tfoot ' . $class . '">';
                    echo $fval;
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

    public function addReportHeader($line) {
        $this->report_header[] = $line;
        return $this;
    }

    public function exportExcel($filename, $sheet_name) {
        $this->export_excel = true;
        $excel = CExcel::factory()->set_creator("cresenity_system")->set_subject("Cresenity Report");
        $excel->set_active_sheet_name($sheet_name);
        $header_count = count($this->report_header);

        $total_column = count($this->columns);
        $addition_column = 0;
        if ($this->numbering)
            $addition_column++;
        if ($total_column < 2)
            $total_column = 2;



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
            $excel->write_by_index($i, $header_count + 1, $col->getLabel());
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

                    case "left": $class .= " align-left";
                        break;
                    case "right": $class .= " align-right";
                        break;
                    case "center": $class .= " align-center";
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
            if ($this->numbering)
                $addition_column++;
            if ($total_column < 2)
                $total_column = 2;
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
                                case "left": $class .= " align-left";
                                    break;
                                case "right": $class .= " align-right";
                                    break;
                                case "center": $class .= " align-center";
                                    break;
                            }
                            $excel->write_by_index($i, $j, $fcolval);
                            $excel->set_align_by_index($i, $j, $col->get_align());
                        }
                        $i++;
                    }
                } else {
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

    public function setExportFilename($filename) {
        $this->export_filename = $filename;
        return $this;
    }

    public function setExportSheetname($sheetname) {
        $this->export_sheetname = $sheetname;
        return $this;
    }

    public function setPdfFontSize($size) {
        $this->pdf_font_size = $size;
        return $this;
    }

    public function getPdfTableAttr() {
        if ($this->export_pdf) {
            return ' border="1" width="100%"';
        }
        return '';
    }

    public function getPdfTHeadTdAttr() {
        if ($this->export_pdf) {
            return ' bgcolor="#9f9f9f" color="#000" style="color:#000" ';
        }
        return '';
    }

    public function getPdfTBodyTdAttr() {
        if ($this->export_pdf) {
            return ' valign="middle"';
        }
        return '';
    }

}
