<?php

defined('SYSPATH') OR die('No direct access allowed.');

class cajax {

    public static function callback($obj, $input) {

        $callable = $obj->data->callable;
        if(is_callable($callable)) {
            return call_user_func($callable, $obj->data);
        }
    }

    public static function form_process($obj, $input) {
        $db = CDatabase::instance();
        $form = $obj->data->form;
        $process_id = "";

        if (isset($input["ajax_process_id"])) {
            $process_id = $input["ajax_process_id"];
        }
        if (isset($input["ajax_process_id"])) {
            $last_process_id = cprogress::last_process_id();
        }
        if (isset($input["cancel"])) {
            $filename = $process_id . "_cancel";
            $file = ctemp::makepath("process", $filename . ".tmp");
            $json = file_put_contents($file, $form);
            echo json_encode(array(
                "result" => "0",
                "message" => "User cancelled",
            ));
            die();
        }

        $filename = $process_id;
        $file = ctemp::makepath("process", $filename . ".tmp");
        $json = '{"percent":0,"info":"Initializing"}';
        if (file_exists($file)) {
            $json = file_get_contents($file);
        }
        echo $json;
    }

    public static function query($obj, $input) {
        $db = CDatabase::instance();
        $q = $obj->data->query;
        $param = $obj->data->param;

        foreach ($param as $p) {

            $val = $input[$p];
            $q = str_replace("{" . $p . "}", $db->escape($val), $q);
        }
        $r = $db->query($q);
        $data_list = $r->result(false);
        $data = array();
        foreach ($data_list as $row) {
            $arr = array();
            foreach ($row as $col => $val) {
                $arr[$col] = $val;
            }
            $data[] = $arr;
        }
        $response = json_encode($data);
        return $response;
    }

    public static function fillselect($obj, $input) {
        $db = CDatabase::instance();
        $q = $obj->query;
        $param = $obj->param;
        foreach ($param as $p) {

            $val = $input[$p->name];
            $q = str_replace("{" . $p->name . "}", $db->escape($val), $q);
        }
        $data_list = cdbutils::get_list($q);
        $response = json_encode($data_list);
        return $response;
    }

    public static function searchselect($obj, $input) {
        $db = CDatabase::instance();
        $q = $obj->data->query;
        $key_field = $obj->data->key_field;
        $search_field = $obj->data->search_field;
        $callback = "";
        $term = "";
        $limit = "";
        $page = "";

        if (isset($input["callback"])) {
            $callback = $input["callback"];
        }
        if (isset($input["term"])) {
            $term = $input["term"];
        }
        if (isset($input["q"])) {
            $term = $input["q"];
        }
        if (isset($input["limit"])) {
            $limit = $input["limit"];
        }
        if (isset($input["page"])) {
            $page = $input["page"];
        }
        $base_q = $q;
        $pos_order_by = strpos(strtolower($base_q), "order by", strpos(strtolower($base_q), 'from'));
        $temp_order_by = '';
        if ($pos_order_by !== false) {
            $temp_order_by = substr($base_q, $pos_order_by, strlen($base_q) - $pos_order_by);
            $base_q = substr($base_q, 0, $pos_order_by);
        }

        $total = cdbutils::get_row_count_from_base_query($q);

        /* Paging */
        $sLimit = "";
        if (strlen($limit) > 0) {
            if (strlen($page) > 0) {
                $sLimit = "LIMIT " . ((intval($page) - 1) * 10) . ", " . intval($limit);
            } else {
                $sLimit = "LIMIT " . intval($limit);
            }
        }


        /* Ordering */
        $sOrder = "";
        /*
          if ( isset( $_GET['iSortCol_0'] ) ) {
          $sOrder = "ORDER BY  ";
          for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ) {
          if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ) {
          $sOrder .= "`".$columns[ intval( $_GET['iSortCol_'.$i] ) ]->fieldname."` ".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
          }
          }

          $sOrder = substr_replace( $sOrder, "", -2 );
          if ( $sOrder == "ORDER BY" ) {
          $sOrder = "";
          }
          }

         */

        $sWhere = "";
        if (strlen($term) > 0 && (!empty($search_field))) {
            $sWhere = "WHERE (";
            if (is_array($search_field)) {
                foreach ($search_field as $f) {
                    $sWhere .= "`" . $f . "` LIKE '%" . mysql_real_escape_string($term) . "%' OR ";
                }
            } else {
                $sWhere .= "`" . $search_field . "` LIKE '%" . mysql_real_escape_string($term) . "%' OR ";
            }

            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';

            //order
            if (is_array($search_field)) {
                foreach ($search_field as $f) {
                    if (strlen($sOrder) > 0)
                        $sOrder.=",";
                    $sOrder.= "`" . $f . "` = '" . mysql_real_escape_string($term) . "' DESC";
                }
            }
        }

        if (strlen($sOrder) > 0) {
            $sOrder = " ORDER BY " . $sOrder;
            $temp_order_by = '';
        }

        if (strlen($temp_order_by) > 0) {
            $sub = explode(",", substr($temp_order_by, 9));
            $temp_order_by = "";
            foreach ($sub as $val) {
                $kata = explode(".", $val);
                if (isset($kata[1]))
                    $temp_order_by.=", " . $kata[1];
                else
                    $temp_order_by.=", " . $kata[0];
            }
            $temp_order_by = substr($temp_order_by, 2);
            $temp_order_by = "ORDER BY " . $temp_order_by;
        }

        $qfilter = "select * from (" . $base_q . ") as a " . $sWhere . ' ' . $sOrder;
        $total = cdbutils::get_row_count_from_base_query($qfilter);


        $qfilter .= " " . $temp_order_by . ' ' . $sLimit;

        $r = $db->query($qfilter);

        $result = $r->result(false);
        $data = array();
        foreach ($r as $row) {
            $p = array();
            foreach ($row as $k => $v) {
                $p[$k] = ($v == null) ? "" : $v;
            }
            $p["id"] = $row[$key_field];
            //$p["id"]=$row["item_id"];
            $data[] = $p;
        }
        $result = array();
        $result["data"] = $data;
        $result["total"] = $total;

        $response = "";
        $response.= $callback . "(";
        $response.= json_encode($result);
        $response.= ")";
        return $response;
    }

    public static function datatable($obj, $input) {

        $q = $obj->data->query;
        $param = $obj->param;
        $js = "";


        foreach ($param as $p) {

            $val = $input[$p->name];
            $q = str_replace("{" . $p->name . "}", $db->escape($val), $q);
        }

        $table = unserialize($obj->data->table);
        //$db = CDatabase::instance($table->domain(),'ctable',$table->db_config);
        $domain = $obj->data->domain;
        $db = CDatabase::instance($domain);


        $request = $_GET;

        if (strtoupper($table->ajax_method) == "POST") {
            $request = $_POST;
        }
        $columns = $obj->data->columns;
        $row_action_list = $table->row_action_list;
        $key = $obj->data->key_field;
        $base_q = $q;


        $pos_order_by = strpos(strtolower($base_q), "order by", strpos(strtolower($base_q), 'from'));
        $temp_order_by = '';
        if ($pos_order_by !== false) {
            $temp_order_by = substr($base_q, $pos_order_by, strlen($base_q) - $pos_order_by);
            $base_q = substr($base_q, 0, $pos_order_by);
        }


        $qtotal = "select count(*) as cnt from (" . $q . ") as a";
        $rtotal = $db->query($qtotal);
        $total_record = 0;
        if ($rtotal->count() > 0)
            $total_record = $rtotal[0]->cnt;

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
                if ($request['bSortable_' . intval($request['iSortCol_' . $i])] == "true") {
                    $sOrder .= "`" . $columns[intval($request['iSortCol_' . $i])]->fieldname . "` " . mysql_real_escape_string($request['sSortDir_' . $i]) . ", ";
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
                if (isset($request['bSearchable_' . $i]) && $request['bSearchable_' . $i] == "true") {
                    $sWhere .= "`" . $columns[$i]->fieldname . "` LIKE '%" . mysql_real_escape_string($request['sSearch']) . "%' OR ";
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
                $qs_condition_str .= "`" . $field_name . "` LIKE '%" . mysql_real_escape_string($value) . "%' AND ";
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
                $sWhere .= "`" . $columns[$i]->fieldname . "` LIKE '%" . mysql_real_escape_string($request['sSearch_' . $i]) . "%' ";
            }
        }

        $qfilter = "select * from (" . $base_q . ") as a " . $sWhere . ' ' . $sOrder;
        if (strlen($sOrder) > 0) {
            $temp_order_by = '';
        }
        $qtotal = "select count(*) as cnt from (" . $qfilter . ") as a";
        $rtotal = $db->query($qtotal);
        $filtered_record = 0;
        if ($rtotal->count() > 0)
            $filtered_record = $rtotal[0]->cnt;


        //die($temp_order_by);
        if (strlen($temp_order_by) > 0) {
            $sub = explode(",", substr($temp_order_by, 9));
            $temp_order_by = "";
            foreach ($sub as $val) {
                $kata = explode(".", $val);
                if (isset($kata[1]))
                    $temp_order_by.=", " . $kata[1];
                else
                    $temp_order_by.=", " . $kata[0];
            }
            $temp_order_by = substr($temp_order_by, 2);
            $temp_order_by = "ORDER BY " . $temp_order_by;
        }
        //die($temp_order_by);
        //$temp_order_by=substr($temp_order_by,0,9).$sub[1];

        $qfilter .= " " . $temp_order_by . ' ' . $sLimit;
        //die(substr($temp_order_by,0,9).$sub[1]);
        //var_dump($db->query('select * from transaction limit 1')->result_array());

        $r = $db->query($qfilter);

        //$filtered_record = $r->count();
        $rarr = $r->result(false);
        $data = $rarr;
        $output = array(
            "sEcho" => intval(carr::get($request, 'sEcho')),
            "iTotalRecords" => $total_record,
            "iTotalDisplayRecords" => $filtered_record,
            "aaData" => array(),
        );
        $no = carr::get($request, 'iDisplayStart', 0);
        foreach ($data as $row) {
            $arr = array();
            $no++;
            $key = "";

            if (array_key_exists($table->key_field, $row)) {

                $key = $row[$table->key_field];
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
                        $js .= $new_v['js'];
                        $new_v = $new_v['html'];
                    }


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
                $arr[] = $new_v;
            }
            if (count($row_action_list) > 0) {
                $html = new CStringBuilder();
                ;
                $html->appendln('<td class="low-padding align-center">')->inc_indent()->br();
                foreach ($row as $k => $v) {
                    $jsparam[$k] = $v;
                }
                $jsparam["param1"] = $key;
                if ($table->action_style == "btn-dropdown") {
                    $table->row_action_list->add_class("pull-right");
                }
                $row_action_list->regenerate_id(true);
                $row_action_list->apply("jsparam", $jsparam);

                $row_action_list->apply("set_handler_url_param", $jsparam);

                if (($table->filter_action_callback_func) != null) {
                    $actions = $row_action_list->childs();

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

                $html->appendln($row_action_list->html($html->get_indent()));
                $js.=$row_action_list->js();
                $html->dec_indent()->appendln('</td>')->br();
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

    public static function handler_reload($obj, $input) {

        $data = $obj->data->json;

        return $data;
    }

}
