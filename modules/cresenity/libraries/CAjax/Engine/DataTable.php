<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 11:06:04 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Engine_DataTable extends CAjax_Engine {

    public function execute() {
        $data = $this->ajaxMethod->getData();

        $isElastic = carr::get($data, 'isElastic');


        $table = unserialize(carr::get($data, 'table'));

        $domain = carr::get($data, 'domain', CF::domain());
        $q = carr::get($data, 'query');

        $js = "";

        $input = $this->input;
        if ($isElastic) {
            return $this->dataElastic();
        }



        $db = CDatabase::instance($domain);


        $request = $_GET;
        if (strtoupper($this->ajaxMethod->getMethod()) == "POST") {
            $request = $_POST;
        }
        $columns = carr::get($data, 'columns');
        $rowActionList = $table->getRowActionList();
        $key = carr::get($data, 'keyField');

        $qBase = $q;

        $maxWhere = strrpos(strtolower($qBase), 'where');
        $maxFrom = strrpos(strtolower($qBase), 'from');

        $maxOffset = 0;
        if ($maxWhere !== false && $maxWhere > $maxOffset) {
            $maxOffset = $maxWhere;
        }
        if ($maxFrom !== false && $maxFrom > $maxOffset) {
            $maxOffset = $maxFrom;
        }

        $posOrderBy = strrpos(strtolower($qBase), "order by", $maxOffset);

        $postLastBracket = strrpos(strtolower($qBase), ")");

        $stringOrderBy = '';
        if ($posOrderBy !== false && $posOrderBy > $postLastBracket) {

            $stringOrderBy = substr($qBase, $pos_order_by, strlen($qBase) - $posOrderBy);
            $qBase = substr($qBase, 0, $posOrderBy);
        }

        // get total record
        $qTotal = "select count(*) as cnt from (" . $q . ") as a";
        $rTotal = $db->query($qTotal);
        $totalRecord = 0;
        if ($rTotal->count() > 0) {
            $totalRecord = $rTotal[0]->cnt;
        }

        /* Paging */
        $sLimit = "";
        if (isset($request['iDisplayStart']) && $request['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . intval($request['iDisplayStart']) . ", " . intval($request['iDisplayLength']);
        }


        /* Ordering */
        $sOrder = "";
        if (isset($request['iSortCol_0'])) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($request['iSortingCols']); $i++) {
                $i2 = 0;
                if ($table->checkbox) {
                    $i2 = -1;
                }
                if ($request['bSortable_' . intval($request['iSortCol_' . $i])] == "true") {
                    $sOrder .= "" . $db->escape_column($columns[intval($request['iSortCol_' . $i]) + $i2]->fieldname) . " " . $db->escape_str($request['sSortDir_' . $i]) . ", ";
                }
            }
            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }

        /**
         * Build condition query
         */
        $sWhere = "";
        $qs_condition_str = "";
        if (isset($request['sSearch']) && $request['sSearch'] != "") {
            for ($i = 0; $i < count($columns); $i++) {
                $i2 = 0;
                if ($table->checkbox) {
                    $i2 = -1;
                }
                if (isset($request['bSearchable_' . $i]) && $request['bSearchable_' . $i] == "true") {
                    $sWhere .= "`" . $columns[$i + $i2]->fieldName . "` LIKE '%" . $db->escape_like($request['sSearch']) . "%' OR ";
                }
            }
            $sWhere = substr_replace($sWhere, "", -3);
        }
        // Quick Search
        $qs_cond = array();
        if (isset($request['dttable_quick_search'])) {
            $qs_cond = json_decode($request['dttable_quick_search'], TRUE);
        }
        if (isset($qs_cond) && count($qs_cond) > 0) {


            foreach ($qs_cond as $qs_cond_k => $qs_cond_v) {
                $value = $qs_cond_v['value'];
                $transforms = carr::get($qs_cond_v, 'transforms');
                if (strlen($transforms) > 0) {
                    $transforms = json_decode($transforms, TRUE);

                    foreach ($transforms as $transforms_k => $transforms_v) {
                        $value = ctransform::$transforms_v['func']($value, TRUE);
                    }
                }

                $field_name = str_replace('dt_table_qs-', '', $qs_cond_v['name']);
                $qs_condition_str .= "`" . $field_name . "` LIKE '%" . $db->escape_like($value) . "%' AND ";
            }
            $qs_condition_str = substr_replace($qs_condition_str, "", -4);
            if (strlen(trim($sWhere)) > 0)
                $sWhere = ' ( ' . $sWhere . ' ) AND ';

            $sWhere .= $qs_condition_str;
        }

        if (strlen($sWhere) > 0) {
            $sWhere = " WHERE ( " . $sWhere . " )";
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($columns); $i++) {
            if (isset($request['bSearchable_' . $i]) && $request['bSearchable_' . $i] == "true" && $request['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere = "WHERE ";
                } else {
                    $sWhere .= " AND ";
                }
                $sWhere .= "`" . $columns[$i]->fieldname . "` LIKE '%" . $db->escape_like($request['sSearch_' . $i]) . "%' ";
            }
        }

        $qFiltered = "select * from (" . $qBase . ") as a " . $sWhere;

        $qTotalFiltered = "select count(*) as cnt from (" . $qFiltered . ") as a";
        $rTotalFiltered = $db->query($qTotalFiltered);
        $totalFilteredRecord = 0;
        if ($rTotalFiltered->count() > 0)
            $totalFilteredRecord = $rTotalFiltered[0]->cnt;

        if (strlen($sOrder) > 0) {
            //order by from paramter found, reset order by from baseQuery
            $stringOrderBy = '';
        }

        if (strlen($stringOrderBy) > 0) {
            //remove prefixed column from order by
            $sub = explode(",", substr($stringOrderBy, 9));
            $stringOrderBy = "";
            foreach ($sub as $val) {
                $columnNames = explode(".", $val);
                $columnName = $columnNames[0];
                if (isset($columnNames[1])) {
                    $columnName = $columnNames[1];
                }
                $stringOrderBy .= ", " . $columnName;
            }
            $stringOrderBy = "ORDER BY " . substr($stringOrderBy, 2);
        }


        $qFiltered .= " " . $sOrder . " " . $stringOrderBy . ' ' . $sLimit;

        $resultQ = $db->query($qFiltered);


        $data = $resultQ->result(false);

        $output = array(
            "sEcho" => intval(carr::get($request, 'sEcho')),
            "iTotalRecords" => $totalRecord,
            "iTotalDisplayRecords" => $totalFilteredRecord,
            "aaData" => array(),
        );
        $no = carr::get($request, 'iDisplayStart', 0);
        foreach ($data as $row) {
            $arr = array();
            $no++;
            $key = "";

            if (array_key_exists($table->keyField, $row)) {

                $key = $row[$table->keyField];
            }
            if ($table->numbering) {
                $arr[] = $no;
            }

            if ($table->checkbox) {
                $arr[] = '<input type="checkbox" name="' . $table->id() . '-check[]" id="' . $table->id() . '-' . $key . '" value="' . $key . '" class="checkbox-' . $table->id() . '">';
            }
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
                if (strlen($col->getFormat()) > 0) {
                    $temp_v = $col->getFormat();
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
                        $js .= $new_v['js'];
                        $new_v = $new_v['html'];
                    }


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
                $arr[] = $new_v;
            }
            if (count($rowActionList) > 0) {
                $html = new CStringBuilder();
                ;
                $html->appendln('<td class="low-padding align-center cell-action td-action">')->inc_indent()->br();
                foreach ($row as $k => $v) {
                    $jsparam[$k] = $v;
                }
                $jsparam["param1"] = $key;
                if ($table->getRowActionList()->getStyle() == "btn-dropdown") {
                    $table->getRowActionList()->add_class("pull-right");
                }
                $rowActionList->regenerateId(true);
                $rowActionList->apply("jsParam", $jsparam);

                $rowActionList->apply("setHandlerUrlParam", $jsparam);

                if (($table->filter_action_callback_func) != null) {
                    $actions = $rowActionList->childs();

                    foreach ($actions as $action) {
                        $visibility = CDynFunction::factory($table->filter_action_callback_func)
                                ->add_param($table)
                                ->add_param($col->get_fieldname())
                                ->add_param($row)
                                ->add_param($action)
                                ->set_require($table->requires)
                                ->execute();

                        $action->set_visibility($visibility);
                    }


                    //call_user_func($this->cell_callback_func,$this,$col->get_fieldname(),$row,$v);
                }

                $html->appendln($table->getRowActionList()->html($html->get_indent()));
                $js .= $table->getRowActionList()->js();
                $html->dec_indent()->appendln('</td>')->br();
                //$arr[] = '';
                $arr[] = $html->text();
                $arr["DT_RowId"] = $key;
            }
            $output["aaData"][] = $arr;
        }


        $data = array(
            "datatable" => $output,
            "js" => cbase64::encode($js),
        );
        $response = json_encode($data);
        return $response;
    }

}
